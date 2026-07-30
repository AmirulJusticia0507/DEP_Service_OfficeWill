@extends('layouts.app')
@section('title', 'Edit Karyawan')
@section('breadcrumbs')
    <span class="text-slate-800 font-medium">Employee Management</span>
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 font-medium">Employee List</span>
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 font-medium">Employee Info</span>
@endsection
@section('quick-menu')
    <a href="#" class="block px-3 py-2 rounded text-sm text-slate-600 hover:bg-slate-100 font-medium">基本情報</a>
    <a href="#" class="block px-3 py-2 rounded text-sm text-slate-600 hover:bg-slate-100">アカウント情報</a>
    <a href="#" class="block px-3 py-2 rounded text-sm text-slate-600 hover:bg-slate-100">所属情報</a>
    <a href="#" class="block px-3 py-2 rounded text-sm text-slate-600 hover:bg-slate-100">登録</a>
@endsection
@section('content')
<h2 class="text-lg font-bold text-[#1e3a8a] mb-4">Edit Karyawan: {{ $employee->full_name }}</h2>

<form method="POST" class="bg-white shadow rounded max-w-3xl">
    @csrf
    @method('PUT')

    <x-form-section-header>Basic Information</x-form-section-header>
    <div class="p-4 space-y-3">
        <div>
            <label class="block text-sm mb-1"><x-required-mark /> NIK Karyawan</label>
            <input type="text" name="employee_code" value="{{ $employee->employee_code }}" required class="form-input">
        </div>
        <div>
            <label class="block text-sm mb-1"><x-required-mark /> Nama Lengkap</label>
            <input type="text" name="full_name" value="{{ $employee->full_name }}" required class="form-input">
        </div>
        <div>
            <label class="block text-sm mb-1">Kana Name</label>
            <input type="text" name="kana_name" value="{{ $employee->kana_name }}" class="form-input">
        </div>
        <div>
            <label class="block text-sm mb-1"><x-required-mark /> Email</label>
            <input type="email" name="email" value="{{ $employee->email }}" required class="form-input">
        </div>
        <div>
            <label class="block text-sm mb-1">No. Telepon</label>
            <input type="text" name="phone_number" value="{{ $employee->phone_number }}" class="form-input">
        </div>
    </div>

    <x-form-section-header>Account Information</x-form-section-header>
    <div class="p-4 space-y-3">
        <div>
            <label class="block text-sm mb-1">Status Akun</label>
            <select name="account_status" class="form-select">
                <option value="ACTIVE" @selected($employee->account_status === 'ACTIVE')>Aktif</option>
                <option value="LOCKED" @selected($employee->account_status === 'LOCKED')>Terkunci</option>
                <option value="INACTIVE" @selected($employee->account_status === 'INACTIVE')>Nonaktif</option>
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">Cakupan Wewenang</label>
            <select name="authority_effective_range" class="form-select">
                <option value="ONLY" @selected($employee->authority_effective_range === 'ONLY')>Afiliasi sendiri</option>
                <option value="BELOW" @selected($employee->authority_effective_range === 'BELOW')>Afiliasi & sub-afiliasi</option>
                <option value="ALL" @selected($employee->authority_effective_range === 'ALL')>Semua afiliasi</option>
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">Kode Afiliasi Wewenang</label>
            <input type="text" name="authority_effective_affiliation_code" value="{{ $employee->authority_effective_affiliation_code }}" class="form-input">
        </div>
    </div>

    <x-form-section-header>Permissions</x-form-section-header>
    <div class="p-4 space-y-2">
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="can_register_employee" value="1" @checked($employee->can_register_employee) class="rounded border-slate-300"> Daftarkan Karyawan</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="can_register_course" value="1" @checked($employee->can_register_course) class="rounded border-slate-300"> Daftarkan Kursus</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="can_setting_attendance" value="1" @checked($employee->can_setting_attendance) class="rounded border-slate-300"> Atur Absensi</label>
    </div>

    <x-action-buttons align="left" class="p-4 border-t border-slate-200">
        <button type="submit" class="btn-primary">Simpan</button>
        <a href="{{ route('employees.index') }}" class="btn-secondary">Kembali</a>
    </x-action-buttons>
</form>
@endsection
