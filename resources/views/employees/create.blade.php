@extends('layouts.app')
@section('title', 'Tambah Karyawan')
@section('content')
<h2 class="text-xl font-bold mb-4">Tambah Karyawan</h2>
<form method="POST" class="max-w-xl bg-white shadow rounded p-6 space-y-3">
    @csrf
    <div>
        <label class="block text-sm mb-1">NIK Karyawan</label>
        <input type="text" name="employee_code" required class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm mb-1">Nama Lengkap</label>
        <input type="text" name="full_name" required class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm mb-1">Kana Name</label>
        <input type="text" name="kana_name" class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm mb-1">Email</label>
        <input type="email" name="email" required class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm mb-1">No. Telepon</label>
        <input type="text" name="phone_number" class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm mb-1">Cakupan Wewenang</label>
        <select name="authority_effective_range" class="w-full border rounded px-3 py-2 text-sm">
            <option value="ONLY">Afiliasi sendiri</option>
            <option value="BELOW">Afiliasi & sub-afiliasi</option>
            <option value="ALL">Semua afiliasi</option>
        </select>
    </div>
    <div>
        <label class="block text-sm mb-1">Kode Afiliasi Wewenang</label>
        <input type="text" name="authority_effective_affiliation_code" class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div class="flex gap-4">
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="can_register_employee"> Daftarkan Karyawan</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="can_register_course"> Daftarkan Kursus</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="can_setting_attendance"> Atur Absensi</label>
    </div>
    <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-2 text-sm">Simpan</button>
</form>
@endsection
