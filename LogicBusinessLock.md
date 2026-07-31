```php
public function handleFailedLogin(Employee $employee)
{
    $employee->increment('password_error_count');

    if ($employee->password_error_count >= 5) {
        $employee->update([
            'account_status' => 'LOCKED',
            'account_locked_at' => now(),
        ]);
        
        throw ValidationException::withMessages([
            'login' => 'Akun Anda terkunci karena salah memasukkan password sebanyak 5 kali. Silakan hubungi admin.',
        ]);
    }
}
```

## Attendance Completion Lock (server-side enforcement)

Diterapkan di `AttendanceController::complete()`:

1. **Ownership**: `enrollment.employee_id` wajib milik karyawan yang login (403).
2. **Todos lock**: semua todo wajib tuntas (tidak ada status `FAILED`).
3. **Deadline lock**: `enrollment_deadline` yang sudah lewat hari ini tidak bisa diselesaikan (edit after lock).
4. **Idempotency**: bila attendance atau status `COMPLETED` sudah ada, request berikutnya tidak membuat duplikat — langsung redirect info.
5. **DB constraint**: `UNIQUE(employee_id, enrollment_id)` di tabel `attendances` menjamin satu attendance per karyawan per enrollment.
6. **Atomicity**: semua penulisan (attendance, status enrollment, file PDF, sertifikat) dijalankan dalam satu `DB::transaction` dengan `lockForUpdate()` terhadap baris enrollment, sehingga double-submit bersamaan (double click) hanya satu yang berhasil.

## Todo & Exam Submission Lock (server-side enforcement)

Diterapkan di `TodoController` (`submitQuestionnaire`, `submitReport`, `submitTest`) dan `ExamController` (`start`, `submit`, `grade`):

1. **Business lock terpusat** `CourseEnrollment::submissionLockReason()`: submission hanya boleh untuk enrollment berstatus `ENROLLED` dan belum lewat `enrollment_deadline` (edit after lock ditolak).
2. **Serialisasi double-submit**: setiap submission dikunci `lockForUpdate()` terhadap baris enrollment (todo) atau baris attempt (exam) di dalam `DB::transaction`, lalu status dicek ulang di dalam lock — double click hanya satu yang berlaku.
3. **DB constraints** sebagai backstop:
   - `UNIQUE(enrollment_id, course_todo_id)` pada `course_todo_responses` — satu respons per todo.
   - `UNIQUE(enrollment_id, course_todo_id, attempt_number)` pada `exam_attempts` — attempt tidak dobel.
   - `UNIQUE(exam_attempt_id, question_id)` pada `exam_answers` — satu jawaban per soal.
4. **Idempotency**: `updateOrCreate` untuk respons todo (resubmit menimpa, tidak membuat baris baru); `ExamController::start` memakai ulang attempt `IN_PROGRESS` yang masih ada, tidak membuat attempt baru.
5. **Atomicity**: penulisan jawaban + attempt + status respons todo dalam satu transaksi; file laporan lama dihapus saat diganti, file baru dibersihkan bila transaksi gagal.