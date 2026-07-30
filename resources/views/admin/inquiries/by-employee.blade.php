@extends('layouts.app')
@section('title', 'Inkuiri per Karyawan')
@section('header-icon')
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 font-medium">Administration</span>
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 font-medium">Inquiry by Employee</span>
@endsection
@section('content')
<h2 class="text-lg font-bold text-[#1e3a8a] mb-4">Inkuiri per Karyawan</h2>

<div class="bg-white rounded shadow mb-4">
    <div class="p-3 border-b border-slate-200">
        <form class="flex gap-2 items-end">
            <div>
                <label class="text-xs text-slate-500 block mb-0.5">Pilih Karyawan</label>
                <select name="employee_id" class="form-select w-64">
                    <option value="">— Pilih karyawan —</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn-primary">Lihat</button>
        </form>
    </div>
</div>

@if($selectedEmployee)
    <h3 class="font-semibold text-sm mb-3 text-[#1e3a8a]">{{ $selectedEmployee->full_name }}</h3>

    <x-data-table :headers="['Kursus', 'Deadline', 'Status', 'Progress']">
        @forelse ($enrollments as $enr)
        <tr>
            <td>{{ $enr->course->course_name }}</td>
            <td>{{ $enr->enrollment_deadline }}</td>
            <td>
                @if($enr->status === 'ENROLLED')
                    <x-status-badge status="ENROLLED">ENROLLED</x-status-badge>
                @elseif($enr->status === 'COMPLETED')
                    <x-status-badge status="COMPLETED">COMPLETED</x-status-badge>
                @else
                    <x-status-badge status="CANCELLED">CANCELLED</x-status-badge>
                @endif
            </td>
            <td>{{ $enr->todoResponses->count() }} todo</td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center text-slate-400 py-6">Tidak ada enrollment.</td></tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $enrollments->links() }}</div>
@endif
@endsection
