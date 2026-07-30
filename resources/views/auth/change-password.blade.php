@extends('layouts.app')
@section('title', 'Ganti Password')
@section('content')
<div class="max-w-md mx-auto">
    <h2 class="text-xl font-bold mb-4">Ganti Password</h2>
    <form method="POST">
        @csrf
        <div class="mb-3">
            <label class="block text-sm mb-1">Password Saat Ini</label>
            <input type="password" name="current_password" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div class="mb-3">
            <label class="block text-sm mb-1">Password Baru</label>
            <input type="password" name="new_password" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div class="mb-3">
            <label class="block text-sm mb-1">Konfirmasi Password Baru</label>
            <input type="password" name="new_password_confirmation" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-2 text-sm">Simpan</button>
    </form>
</div>
@endsection
