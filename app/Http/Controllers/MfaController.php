<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Mail\OtpVerificationMail;
use App\Models\Employee;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class MfaController extends Controller
{
    private const OTP_EXPIRY_MINUTES = 5;

    public function showVerifyForm(): View|RedirectResponse
    {
        $employee = Auth::guard('employee')->user();

        if ($employee) {
            if (session('mfa.completed')) {
                return view('mfa.verify', ['step3' => true]);
            }

            return redirect()->route('dashboard');
        }

        if (! session('mfa.pending_employee_id')) {
            return redirect()->route('login');
        }

        return view('mfa.verify', ['step3' => false]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'otp' => 'required|string|digits:6',
        ]);

        $pendingId = session('mfa.pending_employee_id');

        if (! $pendingId) {
            return redirect()->route('login');
        }

        $employee = Employee::find($pendingId);

        if (
            ! $employee
            || ! $employee->mfa_otp_hash
            || ! $employee->mfa_otp_expires_at
            || now()->greaterThan($employee->mfa_otp_expires_at)
        ) {
            $this->clearPending($request);

            return redirect()->route('login')->withErrors([
                'otp' => __('The verification code has expired. Please log in again.'),
            ]);
        }

        if (! Hash::check($data['otp'], $employee->mfa_otp_hash)) {
            return back()->withErrors([
                'otp' => __('The verification code is incorrect.'),
            ]);
        }

        $employee->update([
            'mfa_otp_hash' => null,
            'mfa_otp_expires_at' => null,
        ]);

        $request->session()->forget('mfa.pending_employee_id');
        $request->session()->put('mfa.completed', true);

        Auth::guard('employee')->login($employee);
        $request->session()->regenerate();

        ActivityLogger::log('login', "User {$employee->email} completed MFA login.", $employee);

        return redirect()->route('mfa.verify');
    }

    public function resend(Request $request): RedirectResponse
    {
        $pendingId = session('mfa.pending_employee_id');

        if (! $pendingId) {
            return redirect()->route('login');
        }

        $employee = Employee::find($pendingId);

        if (! $employee) {
            $this->clearPending($request);

            return redirect()->route('login');
        }

        $this->sendOtp($employee);

        return back()->with('success', __('A new verification code has been sent to your email.'));
    }

    public function cancel(Request $request): RedirectResponse
    {
        $this->clearPending($request);

        return redirect()->route('login');
    }

    public function sendOtp(Employee $employee): void
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $employee->update([
            'mfa_otp_hash' => Hash::make($otp),
            'mfa_otp_expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
        ]);

        Mail::to($employee->email)->send(new OtpVerificationMail(
            $employee->full_name,
            $otp,
            self::OTP_EXPIRY_MINUTES,
        ));

        NotificationHelper::send(
            $employee,
            'otp_sent',
            'Verification Code Sent',
            'A login verification code has been sent to your email.',
            route('mfa.verify')
        );
    }

    private function clearPending(Request $request): void
    {
        $request->session()->forget('mfa.pending_employee_id');
        $request->session()->forget('mfa.completed');
    }
}
