@extends('layouts.app')
@section('title', 'Karyawan')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-xl font-bold">Data Karyawan</h2>
    <a href="{{ route('employees.create') }}" class="bg-indigo-600 text-white rounded px-4 py-2 text-sm">Tambah</a>
</div>

<form class="mb-4 flex gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/email/NIK" class="border rounded px-3 py-1.5 text-sm flex-1">
    <button class="bg-slate-200 rounded px-3 py-1.5 text-sm">Cari</button>
</form>

<table class="w-full text-sm bg-white shadow rounded">
    <thead class="bg-slate-100">
        <tr><th class="p-2 text-left">NIK</th><th class="p-2 text-left">Nama</th><th class="p-2 text-left">Email</th><th class="p-2 text-left">Status</th><th class="p-2"></th></tr>
    </thead>
    <tbody>
        @foreach ($employees as $emp)
        <tr class="border-t">
            <td class="p-2">{{ $emp->employee_code }}</td>
            <td class="p-2">{{ $emp->full_name }}</td>
            <td class="p-2">{{ $emp->email }}</td>
            <td class="p-2">
                <span class="px-2 py-0.5 rounded text-xs font-medium
                    @if($emp->account_status === 'ACTIVE') bg-emerald-100 text-emerald-800
                    @elseif($emp->account_status === 'LOCKED') bg-amber-100 text-amber-800
                    @else bg-rose-100 text-rose-800 @endif">
                    {{ $emp->account_status }}
                </span>
            </td>
            <td class="p-2"><a href="{{ route('employees.edit', $emp) }}" class="text-indigo-600 hover:underline">Edit</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="mt-4">{{ $employees->links() }}</div>
@endsection
