@extends('layouts.app')
@section('title', 'Data Karyawan')
@section('header-icon')
    <i class="ti ti-users"></i>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 dark:text-slate-100 font-medium">Employee Management</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">Employee List</span>
@endsection
@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-bold text-[#1e3a8a]">Data Karyawan</h2>
    <a href="{{ route('employees.create') }}" class="btn-primary">+ Tambah</a>
</div>

<div class="bg-white dark:bg-navy-800 rounded shadow mb-4">
    <div class="p-3 border-b border-slate-200 dark:border-b dark:border-slate-700">
        <form class="flex flex-wrap gap-2 items-end">
            <div>
                <label class="text-xs text-slate-500 dark:text-slate-300 block mb-0.5">Nama (Partial Match)</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama..." class="form-input w-48">
            </div>
            <div>
                <label class="text-xs text-slate-500 dark:text-slate-300 block mb-0.5">Kode Karyawan (Perfect Match)</label>
                <input type="text" name="employee_code" value="{{ request('employee_code') }}" placeholder="NIK..." class="form-input w-36">
            </div>
            <button class="btn-primary">Cari</button>
        </form>
    </div>
</div>

<x-data-table :headers="['NIK', 'Nama', 'Email', 'Status', '']">
    @forelse ($employees as $emp)
    <tr>
        <td>{{ $emp->employee_code }}</td>
        <td class="font-medium">{{ $emp->full_name }}</td>
        <td>{{ $emp->email }}</td>
        <td>
            @if($emp->account_status === 'ACTIVE')
                <x-status-badge status="COMPLETED">Aktif</x-status-badge>
            @elseif($emp->account_status === 'LOCKED')
                <x-status-badge status="ENROLLED">Terkunci</x-status-badge>
            @else
                <x-status-badge status="CANCELLED">Nonaktif</x-status-badge>
            @endif
        </td>
        <td><a href="{{ route('employees.edit', $emp) }}" class="text-[#1e3a8a] hover:underline text-xs font-medium">Edit</a></td>
    </tr>
    @empty
    <tr><td colspan="5" class="text-center text-slate-400 dark:text-slate-500 py-6">Tidak ada data karyawan.</td></tr>
    @endforelse
</x-data-table>

<div class="mt-4">{{ $employees->links() }}</div>
@endsection
