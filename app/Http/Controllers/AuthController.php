<?php

namespace App\Http\Controllers;

use App\Actions\Auth\HandleFailedLoginAction;
use App\Actions\Auth\ReissuePasswordAction;
use App\Helpers\NotificationHelper;
use App\Mail\PasswordChangedMail;
use App\Mail\PasswordReissuedMail;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request, HandleFailedLoginAction $handleFailedLogin): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $employee = Employee::where('email', $credentials['email'])->first();

        if (! $employee) {
            throw ValidationException::withMessages([
                'email' => 'Email tidak terdaftar.',
            ]);
        }

        if ($employee->account_status === 'LOCKED') {
            throw ValidationException::withMessages([
                'email' => 'Akun Anda terkunci. Silakan hubungi admin.',
            ]);
        }

        if (Auth::guard('employee')->validate($credentials)) {
            $employee->update(['password_error_count' => 0]);

            if ($employee->mfa_enabled) {
                $request->session()->forget('mfa.completed');
                $request->session()->put('mfa.pending_employee_id', $employee->id);

                app(MfaController::class)->sendOtp($employee);

                return redirect()->route('mfa.verify');
            }

            Auth::guard('employee')->attempt($credentials);
            $request->session()->forget('mfa.pending_employee_id');
            $request->session()->forget('mfa.completed');
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        $handleFailedLogin->execute($employee);

        return back()->withErrors([
            'email' => 'Password salah.',
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('employee')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $employee = Auth::guard('employee')->user();

        if (! Hash::check($data['current_password'], $employee->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $employee->update(['password' => Hash::make($data['new_password'])]);

        Mail::to($employee->email)->send(new PasswordChangedMail($employee->full_name));

        NotificationHelper::send(
            $employee,
            'password_changed',
            'Password Changed',
            'Your password has been changed successfully.',
            route('change-password')
        );

        return back()->with('success', 'Password berhasil diubah.');
    }

    public function reissuePassword(Request $request, ReissuePasswordAction $reissuePassword): RedirectResponse
    {
        $data = $request->validate(['email' => 'required|email']);

        $employee = Employee::where('email', $data['email'])->first();

        if (! $employee) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        $newPassword = $reissuePassword->execute($employee);

        Mail::to($employee->email)->send(new PasswordReissuedMail(
            $employee->full_name,
            $newPassword,
            config('app.url').'/login',
        ));

        NotificationHelper::send(
            $employee,
            'password_reissued',
            'Password Reissued',
            'Your password has been reset. Check your email for the new password.',
            route('login')
        );

        return back()->with('success', 'Password baru telah dikirim ke email Anda.');
    }
}
