@extends('layouts.app')
@section('title', 'Edit Profile')
@section('header-icon')<i class="ti ti-user-edit"></i>@endsection
@section('breadcrumbs')
    <a href="{{ route('profile.show') }}" class="text-slate-400 hover:text-slate-600">{{ __('My Profile') }}</a>
    <span class="mx-1">/</span>
    <span class="text-slate-800 font-medium dark:text-slate-100">{{ __('Edit') }}</span>
@endsection
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded shadow p-6 dark:bg-navy-800">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Name') }} <span class="required-mark">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name', $employee->full_name) }}" class="form-input" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Name') }} (Kana)</label>
                    <input type="text" name="kana_name" value="{{ old('kana_name', $employee->kana_name) }}" class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Phone') }}</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $employee->phone_number) }}" class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Place of Birth') }}</label>
                    <input type="text" name="place_of_birth" value="{{ old('place_of_birth', $employee->place_of_birth) }}" class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Date of Birth') }}</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth?->format('Y-m-d')) }}" class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Gender') }}</label>
                    <select name="gender" class="form-select">
                        <option value="">{{ __('Please select') }}</option>
                        <option value="MALE" @if($employee->gender === 'MALE') selected @endif>{{ __('Male') }}</option>
                        <option value="FEMALE" @if($employee->gender === 'FEMALE') selected @endif>{{ __('Female') }}</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium mb-1">{{ __('Address') }}</label>
                <textarea name="address" rows="3" class="form-input">{{ old('address', $employee->address) }}</textarea>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium mb-1">{{ __('Photo') }}</label>
                @if($employee->photo)
                <div class="mb-2">
                    <img src="{{ Storage::url($employee->photo) }}" class="w-20 h-20 rounded object-cover">
                </div>
                @endif
                <input type="file" name="photo" accept="image/*" class="form-input">
            </div>
            <div class="mt-6 border-t border-slate-200 dark:border-slate-700 pt-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="mfa_enabled" value="1" @checked($employee->mfa_enabled) class="mt-0.5 w-4 h-4 accent-[var(--primary)]">
                    <span>
                        <span class="block text-sm font-medium">{{ __('Two-Step Verification (MFA)') }}</span>
                        <span class="block text-xs text-slate-400">{{ __('Require a one-time code sent to your email after login.') }}</span>
                    </span>
                </label>
            </div>
            <div class="flex items-center gap-2 mt-6">
                <button type="submit" class="btn-primary">{{ __('Save') }}</button>
                <a href="{{ route('profile.show') }}" class="btn-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
