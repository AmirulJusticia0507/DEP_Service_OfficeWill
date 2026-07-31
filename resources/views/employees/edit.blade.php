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
    <form method="POST" action="{{ route('employees.update', $employee) }}" class="bg-white rounded shadow dark:bg-navy-800" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-primary text-white px-4 py-3 rounded-t">
            <h3 class="text-sm font-semibold">{{ __('Edit') }}: {{ $employee->full_name }}</h3>
        </div>

        <div class="p-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1"><x-required-mark /> {{ __('Code') }}</label>
                    <input type="text" name="employee_code" value="{{ old('employee_code', $employee->employee_code) }}" required class="form-input @error('employee_code') border-red-mark @enderror">
                    <x-field-error field="employee_code" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1"><x-required-mark /> {{ __('Name') }}</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $employee->full_name) }}" required class="form-input @error('full_name') border-red-mark @enderror">
                    <x-field-error field="full_name" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Name') }} (Kana)</label>
                    <input type="text" name="kana_name" value="{{ old('kana_name', $employee->kana_name) }}" class="form-input @error('kana_name') border-red-mark @enderror">
                    <x-field-error field="kana_name" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1"><x-required-mark /> {{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $employee->email) }}" required class="form-input @error('email') border-red-mark @enderror">
                    <x-field-error field="email" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Phone') }}</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $employee->phone_number) }}" class="form-input @error('phone_number') border-red-mark @enderror">
                    <x-field-error field="phone_number" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Place of Birth') }}</label>
                    <input type="text" name="place_of_birth" value="{{ old('place_of_birth', $employee->place_of_birth) }}" class="form-input @error('place_of_birth') border-red-mark @enderror">
                    <x-field-error field="place_of_birth" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Date of Birth') }}</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth?->format('Y-m-d')) }}" class="form-input @error('date_of_birth') border-red-mark @enderror">
                    <x-field-error field="date_of_birth" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Gender') }}</label>
                    <select name="gender" class="form-select @error('gender') border-red-mark @enderror">
                        <option value="">{{ __('Please select') }}</option>
                        <option value="MALE" @selected(old('gender', $employee->gender) === 'MALE')>{{ __('Male') }}</option>
                        <option value="FEMALE" @selected(old('gender', $employee->gender) === 'FEMALE')>{{ __('Female') }}</option>
                    </select>
                    <x-field-error field="gender" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">{{ __('Address') }}</label>
                <textarea name="address" rows="2" class="form-input @error('address') border-red-mark @enderror">{{ old('address', $employee->address) }}</textarea>
                <x-field-error field="address" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">{{ __('Photo') }}</label>
                @if($employee->photo)
                <div class="mb-2"><img src="{{ Storage::url($employee->photo) }}" class="w-16 h-16 rounded object-cover"></div>
                @endif
                <input type="file" name="photo" accept="image/*" class="form-input @error('photo') border-red-mark @enderror">
                <x-field-error field="photo" />
            </div>
        </div>

        @if($canManageAuthorities)
        <div class="bg-primary text-white px-4 py-3">
            <h3 class="text-sm font-semibold">{{ __('Account Information') }}</h3>
        </div>
        <div class="p-4 space-y-3">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Status') }}</label>
                    <select name="account_status" class="form-select @error('account_status') border-red-mark @enderror">
                        <option value="ACTIVE" @selected(old('account_status', $employee->account_status) === 'ACTIVE')>Aktif</option>
                        <option value="LOCKED" @selected(old('account_status', $employee->account_status) === 'LOCKED')>Terkunci</option>
                        <option value="INACTIVE" @selected(old('account_status', $employee->account_status) === 'INACTIVE')>Nonaktif</option>
                    </select>
                    <x-field-error field="account_status" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Cakupan Wewenang</label>
                    <select name="authority_effective_range" class="form-select @error('authority_effective_range') border-red-mark @enderror">
                        <option value="ONLY" @selected(old('authority_effective_range', $employee->authority_effective_range) === 'ONLY')>Afiliasi sendiri</option>
                        <option value="BELOW" @selected(old('authority_effective_range', $employee->authority_effective_range) === 'BELOW')>Afiliasi & sub-afiliasi</option>
                        <option value="ALL" @selected(old('authority_effective_range', $employee->authority_effective_range) === 'ALL')>Semua afiliasi</option>
                    </select>
                    <x-field-error field="authority_effective_range" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Kode Afiliasi Wewenang</label>
                <input type="text" name="authority_effective_affiliation_code" value="{{ old('authority_effective_affiliation_code', $employee->authority_effective_affiliation_code) }}" class="form-input @error('authority_effective_affiliation_code') border-red-mark @enderror">
                <x-field-error field="authority_effective_affiliation_code" />
            </div>
        </div>

        <div class="bg-primary text-white px-4 py-3">
            <h3 class="text-sm font-semibold">Permissions</h3>
        </div>
        <div class="p-4 space-y-2">
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="can_register_employee" value="1" @checked($employee->can_register_employee) class="rounded border-slate-300 dark:border-slate-600 dark:bg-navy-800"> Daftarkan Karyawan</label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="can_register_course" value="1" @checked($employee->can_register_course) class="rounded border-slate-300 dark:border-slate-600 dark:bg-navy-800"> Daftarkan Kursus</label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="can_setting_attendance" value="1" @checked($employee->can_setting_attendance) class="rounded border-slate-300 dark:border-slate-600 dark:bg-navy-800"> Atur Absensi</label>
        </div>
        @endif

        <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700 flex items-center gap-2">
            <button type="submit" class="btn-primary">{{ __('Save') }}</button>
            <a href="{{ route('employees.index') }}" class="btn-secondary">{{ __('Back to List') }}</a>
        </div>
    </form>

    <div class="bg-white rounded shadow dark:bg-navy-800 mt-6">
        <div class="bg-primary text-white px-4 py-3 rounded-t">
            <h3 class="text-sm font-semibold">Masa Jabatan & Periode Afiliasi</h3>
        </div>
        <div class="p-4 space-y-3">
            @forelse ($assignments as $ea)
            <div class="flex items-center justify-between border border-slate-200 dark:border-slate-700 rounded p-3">
                <div>
                    <p class="text-sm font-medium">{{ $ea->affiliation->affiliation_name ?? $ea->affiliation_code }}</p>
                    <p class="text-xs text-slate-500">
                        {{ $ea->job->job_title ?? '-' }}
                        &middot; {{ $ea->start_date?->format('d M Y') }} - {{ $ea->end_date?->format('d M Y') ?? 'Sekarang' }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    @if (! $ea->end_date)
                    <form method="POST" action="{{ route('employees.assignments.end', [$employee, $ea]) }}">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn-secondary" onclick="return confirm('Tutup penugasan ini?')">Tutup</button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('employees.assignments.destroy', [$employee, $ea]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-rose-500 hover:underline text-xs ml-2" onclick="return confirm('Hapus penugasan ini?')">Hapus</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-sm text-slate-400">Belum ada penugasan.</p>
            @endforelse
        </div>

        <div class="border-t border-slate-200 dark:border-slate-700 p-4">
            <h4 class="text-sm font-semibold mb-2">Tambah Penugasan</h4>
            <form method="POST" action="{{ route('employees.assignments.store', $employee) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Afiliasi</label>
                    <select name="affiliation_code" class="form-select @error('affiliation_code') border-red-mark @enderror" required>
                        <option value="">Pilih afiliasi</option>
                        @foreach ($affiliations as $af)
                        <option value="{{ $af->affiliation_code }}" @selected(old('affiliation_code') === $af->affiliation_code)>{{ $af->affiliation_name }} ({{ $af->affiliation_code }})</option>
                        @endforeach
                    </select>
                    <x-field-error field="affiliation_code" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Jabatan</label>
                    <select name="job_id" class="form-select @error('job_id') border-red-mark @enderror">
                        <option value="">Tidak ada</option>
                        @foreach ($jobs as $job)
                        <option value="{{ $job->job_id }}" @selected(old('job_id') === $job->job_id)>{{ $job->job_title }}</option>
                        @endforeach
                    </select>
                    <x-field-error field="job_id" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Mulai</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required class="form-input @error('start_date') border-red-mark @enderror">
                    <x-field-error field="start_date" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Berakhir (opsional)</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-input @error('end_date') border-red-mark @enderror">
                    <x-field-error field="end_date" />
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="btn-primary">Tambah Penugasan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
