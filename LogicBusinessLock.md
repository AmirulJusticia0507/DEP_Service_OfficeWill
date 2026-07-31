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

## Assignment Period Lock (masa jabatan / periode afiliasi)

Diterapkan di `EmployeeController` (`storeAssignment`, `endAssignment`, `destroyAssignment`):

1. **Authorization**: hanya operator dengan `can_register_employee` dan cakupan `canAccessEmployee` terhadap karyawan tersebut.
2. **Scope afiliasi**: kode afiliasi wajib milik company yang sama (`exists:...company_id`) dan dalam cakupan operator (`ScopeService::canAccessAffiliation`), selain itu `403`.
3. **Overlap lock**: periode baru (incl. open-ended `end_date` null) ditolak bila tumpang tindih dengan penugasan yang sudah ada untuk karyawan yang sama — baik kode afiliasi yang sama maupun `job_id` yang sama. Dijalankan di dalam `DB::transaction` dengan `lockForUpdate()` terhadap baris employee sehingga dua create bersamaan tidak bisa lolos.
4. **Edit after lock**: penugasan yang sudah ditutup (`end_date` terisi) tidak dapat ditutup lagi; `end_date` tidak boleh sebelum `start_date`.