@extends('layouts.app')
@section('title', 'Verifikasi 2 Langkah')
@section('content')
<div class="max-w-md mx-auto my-16">
    <div class="bg-white dark:bg-navy-800 shadow rounded p-8">
        <div class="text-center mb-8">
            <img src="/officewill_logo_yogya.svg" alt="OfficeWill" class="h-10 mx-auto">
            <p class="text-xs text-accent mt-2 font-medium">DEP Service</p>
        </div>

        <h1 class="text-lg font-semibold text-center mb-6">{{ __('Two-Step Verification') }}</h1>

        {{-- Stepper --}}
        <ol class="space-y-4 mb-8">
            <li class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                    <i class="ti ti-check text-sm"></i>
                </span>
                <div>
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ __('Enter Email & Password') }}</p>
                    <p class="text-xs text-slate-400">{{ __('Login form') }}</p>
                </div>
            </li>
            <li class="flex items-center gap-3">
                @if($step3)
                <span class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                    <i class="ti ti-check text-sm"></i>
                </span>
                <div>
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ __('Enter Verification Code') }}</p>
                    <p class="text-xs text-slate-400">{{ __('OTP via email') }}</p>
                </div>
                @else
                <span class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center shrink-0" style="background-color: var(--primary);">
                    <span class="text-xs font-bold">2</span>
                </span>
                <div>
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ __('Enter Verification Code') }}</p>
                    <p class="text-xs text-slate-400">{{ __('We sent a 6-digit code to your email') }}</p>
                </div>
                @endif
            </li>
            <li class="flex items-center gap-3">
                @if($step3)
                <span class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                    <i class="ti ti-check text-sm"></i>
                </span>
                @else
                <span class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-300 flex items-center justify-center shrink-0">
                    <span class="text-xs font-bold">3</span>
                </span>
                @endif
                <div>
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ __('Done') }}</p>
                    <p class="text-xs text-slate-400">{{ __('You are now signed in') }}</p>
                </div>
            </li>
        </ol>

        @if($step3)
            <div class="text-center">
                <div class="bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 rounded p-4 mb-6">
                    <i class="ti ti-circle-check text-3xl block mb-2"></i>
                    <p class="text-sm font-medium">{{ __('Verification successful. You are now signed in.') }}</p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn-primary w-full py-2 inline-block text-center">
                    {{ __('Go to Dashboard') }}
                </a>
            </div>
        @else
            @if(session('success'))
                <div class="bg-emerald-100 text-emerald-800 px-4 py-3 rounded mb-4 text-sm border-l-4 border-emerald-500 dark:bg-emerald-900 dark:text-emerald-200">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('mfa.verify') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">{{ __('Verification Code') }}</label>
                    <input type="text" name="otp" inputmode="numeric" maxlength="6" autocomplete="one-time-code"
                           placeholder="••••••" required autofocus
                           class="form-input text-center tracking-[0.5em] text-lg @error('otp') border-red-mark @enderror">
                    @error('otp') <p class="text-red-mark text-xs mt-1">{{ $message }}</p> @enderror
                    @error('email') <p class="text-red-mark text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="btn-primary w-full py-2">{{ __('Verify & Continue') }}</button>
            </form>

            <div class="mt-4 flex items-center justify-between text-xs">
                <form method="POST" action="{{ route('mfa.resend') }}">
                    @csrf
                    <button type="submit" class="text-accent hover:underline" style="color: var(--accent);">{{ __('Resend code') }}</button>
                </form>
                <form method="POST" action="{{ route('mfa.cancel') }}">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">{{ __('Cancel') }}</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
