@extends('layouts.app')
@section('title', 'Inkuiri per Kursus')
@section('header-icon')
    <i class="ti ti-search"></i>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 dark:text-slate-100 font-medium">Administration</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">Inquiry by Course</span>
@endsection
@section('content')
<h2 class="text-lg font-bold text-[#1e3a8a] mb-4">Inkuiri per Kursus</h2>

<div class="bg-white dark:bg-navy-800 rounded shadow mb-4">
    <div class="p-3 border-b border-slate-200 dark:border-b dark:border-slate-700">
        <form class="flex gap-2 items-end">
            <div>
                <label class="text-xs text-slate-500 dark:text-slate-300 block mb-0.5">Pilih Kursus</label>
                <select name="course_id" class="form-select w-64">
                    <option value="">— Pilih kursus —</option>
                    @foreach ($courses as $c)
                        <option value="{{ $c->id }}" @selected(request('course_id') == $c->id)>{{ $c->course_name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn-primary">Lihat</button>
        </form>
    </div>
</div>

@if($selectedCourse)
    <h3 class="font-semibold text-sm mb-3 text-[#1e3a8a]">{{ $selectedCourse->course_name }}</h3>

    <x-data-table :headers="['Karyawan', 'Deadline', 'Status', 'Progress Todo']">
        @forelse ($enrollments as $enr)
        <tr>
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
            <td>{{ $enr->todoResponses->count() }} todo</td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center text-slate-400 dark:text-slate-500 py-6">Tidak ada enrollment.</td></tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $enrollments->links() }}</div>
@endif
@endsection
