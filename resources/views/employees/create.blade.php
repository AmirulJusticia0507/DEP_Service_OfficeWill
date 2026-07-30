@extends('layouts.app')
@section('title', 'Tambah Karyawan')
@section('header-icon')
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 dark:text-slate-100 font-medium">Employee Management</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">Employee Registration</span>
@endsection
@section('quick-menu')
    <a href="#" class="block px-3 py-2 rounded text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-navy-700 font-medium">基本情報</a>
    <a href="#" class="block px-3 py-2 rounded text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-navy-700">アカウント情報</a>
    <a href="#" class="block px-3 py-2 rounded text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-navy-700">所属情報</a>
    <a href="#" class="block px-3 py-2 rounded text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-navy-700">登録</a>
@endsection
@section('content')
<h2 class="text-lg font-bold text-[#1e3a8a] mb-4">Tambah Karyawan</h2>

<form method="POST" class="bg-white dark:bg-navy-800 shadow rounded max-w-3xl">
    @csrf

    <x-form-section-header>Basic Information</x-form-section-header>
    <div class="p-4 space-y-3">
        <div>
            <label class="block text-sm mb-1"><x-required-mark /> NIK Karyawan</label>
            <input type="text" name="employee_code" required class="form-input">
        </div>
        <div>
            <label class="block text-sm mb-1"><x-required-mark /> Nama Lengkap</label>
            <input type="text" name="full_name" required class="form-input">
        </div>
        <div>
            <label class="block text-sm mb-1">Kana Name</label>
            <input type="text" name="kana_name" class="form-input">
        </div>
        <div>
            <label class="block text-sm mb-1"><x-required-mark /> Email</label>
            <input type="email" name="email" required class="form-input">
        </div>
        <div>
            <label class="block text-sm mb-1">No. Telepon</label>
            <input type="text" name="phone_number" class="form-input">
        </div>
    </div>

    <x-form-section-header>Account Information</x-form-section-header>
    <div class="p-4 space-y-3">
        <div>
            <label class="block text-sm mb-1">Cakupan Wewenang</label>
            <select name="authority_effective_range" class="form-select">
                <option value="ONLY">Afiliasi sendiri</option>
                <option value="BELOW">Afiliasi & sub-afiliasi</option>
                <option value="ALL">Semua afiliasi</option>
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">Kode Afiliasi Wewenang</label>
            <input type="text" name="authority_effective_affiliation_code" class="form-input">
        </div>
    </div>

    <x-form-section-header>Permissions</x-form-section-header>
    <div class="p-4 space-y-2">
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="can_register_employee" class="rounded border-slate-300 dark:border-slate-600 dark:bg-navy-800"> Daftarkan Karyawan</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="can_register_course" class="rounded border-slate-300 dark:border-slate-600 dark:bg-navy-800"> Daftarkan Kursus</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="can_setting_attendance" class="rounded border-slate-300 dark:border-slate-600 dark:bg-navy-800"> Atur Absensi</label>
    </div>

    <x-action-buttons align="left" class="p-4 border-t border-slate-200 dark:border-t dark:border-slate-700">
        <button type="submit" class="btn-primary">Simpan</button>
        <a href="{{ route('employees.index') }}" class="btn-secondary">Kembali</a>
    </x-action-buttons>
</form>
@endsection
