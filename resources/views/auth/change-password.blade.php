@extends('layouts.app')
@section('title', 'Ganti Password')
@section('header-icon')
    <i class="ti ti-lock"></i>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 dark:text-slate-100 font-medium">Account Settings</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">Change Password</span>
@endsection
@section('content')
<div class="max-w-lg mx-auto">
    <h2 class="text-lg font-bold text-[#1e3a8a] mb-4">Ganti Password</h2>
    <form method="POST" class="bg-white dark:bg-navy-800 shadow rounded">
        @csrf

        <x-form-section-header>Password Change</x-form-section-header>
        <div class="p-4 space-y-3">
            <div>
                <label class="block text-sm mb-1"><x-required-mark /> Password Saat Ini</label>
                <input type="password" name="current_password" required class="form-input">
            </div>
            <div>
                <label class="block text-sm mb-1"><x-required-mark /> Password Baru</label>
                <input type="password" name="new_password" required class="form-input">
            </div>
            <div>
                <label class="block text-sm mb-1"><x-required-mark /> Konfirmasi Password Baru</label>
                <input type="password" name="new_password_confirmation" required class="form-input">
            </div>
        </div>

        <x-action-buttons align="left" class="p-4 border-t border-slate-200 dark:border-t dark:border-slate-700">
            <button type="submit" class="btn-primary">Simpan</button>
        </x-action-buttons>
    </form>
</div>
@endsection
