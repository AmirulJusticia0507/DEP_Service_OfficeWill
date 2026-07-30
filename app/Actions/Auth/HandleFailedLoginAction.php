<?php

namespace App\Actions\Auth;

use App\Mail\AccountLockedMail;
use App\Models\Employee;
use Illuminate\Support\Facades\Mail;
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

            try {
                Mail::to($employee->email)->queue(new AccountLockedMail(
                    $employee->full_name,
                    $employee->email,
                ));
            } catch (\Exception $e) {
                // email failed silently
            }

            throw ValidationException::withMessages([
                'login' => 'Akun Anda terkunci karena salah memasukkan password sebanyak 5 kali. Silakan hubungi admin.',
            ]);
        }
    }
}
