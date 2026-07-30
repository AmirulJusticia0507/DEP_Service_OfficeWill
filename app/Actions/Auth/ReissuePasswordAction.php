<?php

namespace App\Actions\Auth;

use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ReissuePasswordAction
{
    public function execute(Employee $employee): string
    {
        $newPassword = Str::random(12);
        $employee->update([
            'password' => Hash::make($newPassword),
            'password_error_count' => 0,
            'account_status' => 'ACTIVE',
            'account_locked_at' => null,
        ]);

        return $newPassword;
    }
}
