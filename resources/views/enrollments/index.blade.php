@extends('layouts.app')
@section('title', 'Enrollment')
@section('breadcrumbs')
    <span class="text-slate-800 font-medium">Enrollment Management</span>
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 font-medium">Enrollment List</span>
@endsection
@section('content')
<h2 class="text-lg font-bold text-[#1e3a8a] mb-4">Enrollment Kursus</h2>

<div class="bg-white rounded shadow mb-4">
    <div class="p-3 border-b border-slate-200">
        <form class="flex flex-wrap gap-2 items-end">
            <div>
                <label class="text-xs text-slate-500 block mb-0.5">Course ID</label>
                <input type="text" name="course_id" value="{{ request('course_id') }}" class="form-input w-36">
            </div>
            <div>
                <label class="text-xs text-slate-500 block mb-0.5">Employee ID</label>
                <input type="text" name="employee_id" value="{{ request('employee_id') }}" class="form-input w-36">
            </div>
            <div>
                <label class="text-xs text-slate-500 block mb-0.5">Status</label>
                <select name="status" class="form-select w-36">
                    <option value="">Semua status</option>
                    <option value="ENROLLED" @selected(request('status') === 'ENROLLED')>ENROLLED</option>
                    <option value="COMPLETED" @selected(request('status') === 'COMPLETED')>COMPLETED</option>
                    <option value="CANCELLED" @selected(request('status') === 'CANCELLED')>CANCELLED</option>
                </select>
            </div>
            <button class="btn-primary">Filter</button>
        </form>
    </div>
</div>

<x-data-table :headers="['Kursus', 'Karyawan', 'Deadline', 'Status', '']">
    @forelse ($enrollments as $enr)
    <tr>
        <td>{{ $enr->course->course_name }}</td>
        <td>{{ $enr->employee->full_name }}</td>
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
        <td>
            @if($enr->status === 'ENROLLED')
            <form method="POST" action="{{ route('enrollments.update', $enr) }}" class="inline">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="CANCELLED">
                <button class="text-[#dc2626] hover:underline text-xs">Batalkan</button>
            </form>
            @endif
        </td>
    </tr>
    @empty
    <tr><td colspan="5" class="text-center text-slate-400 py-6">Tidak ada enrollment.</td></tr>
    @endforelse
</x-data-table>

<div class="mt-4">{{ $enrollments->links() }}</div>
@endsection
