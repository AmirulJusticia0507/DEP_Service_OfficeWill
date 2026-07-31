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