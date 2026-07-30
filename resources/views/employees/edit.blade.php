@extends('layouts.app')
@section('title', 'Edit Karyawan')
@section('header-icon')<i class="ti ti-users"></i>@endsection
@section('breadcrumbs')
    <span class="text-slate-800 font-medium dark:text-slate-100">{{ __('Employees') }}</span>
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 font-medium dark:text-slate-100">{{ $employee->full_name }}</span>
@endsection
@section('content')
<div class="max-w-3xl mx-auto">
    <form method="POST" class="bg-white rounded shadow dark:bg-navy-800" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-maroon-700 text-white px-4 py-3 rounded-t">
            <h3 class="text-sm font-semibold">{{ __('Edit') }}: {{ $employee->full_name }}</h3>
        </div>

        <div class="p-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1"><x-required-mark /> {{ __('Code') }}</label>
                    <input type="text" name="employee_code" value="{{ $employee->employee_code }}" required class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1"><x-required-mark /> {{ __('Name') }}</label>
                    <input type="text" name="full_name" value="{{ $employee->full_name }}" required class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Name') }} (Kana)</label>
                    <input type="text" name="kana_name" value="{{ $employee->kana_name }}" class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1"><x-required-mark /> {{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ $employee->email }}" required class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Phone') }}</label>
                    <input type="text" name="phone_number" value="{{ $employee->phone_number }}" class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Place of Birth') }}</label>
                    <input type="text" name="place_of_birth" value="{{ $employee->place_of_birth }}" class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Date of Birth') }}</label>
                    <input type="date" name="date_of_birth" value="{{ $employee->date_of_birth?->format('Y-m-d') }}" class="form-input">
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

            <div>
                <label class="block text-sm font-medium mb-1">{{ __('Address') }}</label>
                <textarea name="address" rows="2" class="form-input">{{ $employee->address }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">{{ __('Photo') }}</label>
                @if($employee->photo)
                <div class="mb-2"><img src="{{ Storage::url($employee->photo) }}" class="w-16 h-16 rounded object-cover"></div>
                @endif
                <input type="file" name="photo" accept="image/*" class="form-input">
            </div>
        </div>

        <div class="bg-maroon-700 text-white px-4 py-3">
            <h3 class="text-sm font-semibold">{{ __('Account Information') }}</h3>
        </div>
        <div class="p-4 space-y-3">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Status') }}</label>
                    <select name="account_status" class="form-select">
                        <option value="ACTIVE" @selected($employee->account_status === 'ACTIVE')>Aktif</option>
                        <option value="LOCKED" @selected($employee->account_status === 'LOCKED')>Terkunci</option>
                        <option value="INACTIVE" @selected($employee->account_status === 'INACTIVE')>Nonaktif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Cakupan Wewenang</label>
                    <select name="authority_effective_range" class="form-select">
                        <option value="ONLY" @selected($employee->authority_effective_range === 'ONLY')>Afiliasi sendiri</option>
                        <option value="BELOW" @selected($employee->authority_effective_range === 'BELOW')>Afiliasi & sub-afiliasi</option>
                        <option value="ALL" @selected($employee->authority_effective_range === 'ALL')>Semua afiliasi</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Kode Afiliasi Wewenang</label>
                <input type="text" name="authority_effective_affiliation_code" value="{{ $employee->authority_effective_affiliation_code }}" class="form-input">
            </div>
        </div>

        <div class="bg-maroon-700 text-white px-4 py-3">
            <h3 class="text-sm font-semibold">Permissions</h3>
        </div>
        <div class="p-4 space-y-2">
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="can_register_employee" value="1" @checked($employee->can_register_employee) class="rounded border-slate-300 dark:border-slate-600 dark:bg-navy-800"> Daftarkan Karyawan</label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="can_register_course" value="1" @checked($employee->can_register_course) class="rounded border-slate-300 dark:border-slate-600 dark:bg-navy-800"> Daftarkan Kursus</label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="can_setting_attendance" value="1" @checked($employee->can_setting_attendance) class="rounded border-slate-300 dark:border-slate-600 dark:bg-navy-800"> Atur Absensi</label>
        </div>

        <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700 flex items-center gap-2">
            <button type="submit" class="btn-primary">{{ __('Save') }}</button>
            <a href="{{ route('employees.index') }}" class="btn-secondary">{{ __('Back to List') }}</a>
        </div>
    </form>
</div>
@endsection
