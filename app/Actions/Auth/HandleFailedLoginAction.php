<?php

namespace App\Actions\Auth;

use App\Models\Employee;
use Illuminate\Validation\ValidationException;

class HandleFailedLoginAction
{
    public function execute(Employee $employee): void
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
}
