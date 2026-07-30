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