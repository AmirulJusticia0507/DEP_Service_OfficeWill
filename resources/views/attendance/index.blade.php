@extends('layouts.app')
@section('title', 'Confirm and attend courses')
@section('breadcrumbs')
    <span class="text-slate-800 font-medium">In-house training</span>
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 font-medium">Confirm and attend courses</span>
@endsection
@section('content')
<h2 class="text-lg font-bold text-[#1e3a8a] mb-4">Employee Course List</h2>

<x-data-table :headers="['Course Name', 'Classification', 'Attendance Deadline', 'ToDo Status']">
    @forelse ($enrollments as $enr)
    @php
        $totalTodos = $enr->course->todos->count();
        $completedTodos = $enr->todoResponses->count();
        $hasFailed = $enr->todoResponses->where('status', 'FAILED')->isNotEmpty();
    @endphp
    <tr class="cursor-pointer hover:bg-slate-50" onclick="window.location='{{ route('attendance.show', $enr->course_id) }}'">
        <td class="font-medium">{{ $enr->course->course_name }}</td>
        <td>{{ $enr->course->categoryDetail->detail_name ?? '-' }}</td>
        <td>{{ $enr->enrollment_deadline }}</td>
        <td>
            @if($completedTodos === 0)
                <x-status-badge status="CANCELLED">未対応</x-status-badge>
            @elseif($completedTodos < $totalTodos || $hasFailed)
                <x-status-badge status="ENROLLED">回答中</x-status-badge>
            @else
                <x-status-badge status="COMPLETED">修了</x-status-badge>
            @endif
        </td>
    </tr>
    @empty
    <tr><td colspan="4" class="text-center text-slate-400 py-6">Tidak ada kursus aktif.</td></tr>
    @endforelse
</x-data-table>

<div class="mt-4">{{ $enrollments->links() }}</div>
@endsection
