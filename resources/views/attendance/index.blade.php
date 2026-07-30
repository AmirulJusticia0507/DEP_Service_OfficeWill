@extends('layouts.app')
@section('title', 'Confirm and attend courses')
@section('header-icon')<i class="ti ti-calendar-check"></i>@endsection
@section('breadcrumbs')
    <span class="text-slate-800 font-medium dark:text-slate-100">{{ __('Attendance') }}</span>
@endsection
@section('content')
<div class="bg-white rounded shadow overflow-hidden dark:bg-navy-800">
    <div class="bg-primary text-white px-4 py-3 flex items-center justify-between">
        <h3 class="text-sm font-semibold"><i class="ti ti-list"></i> {{ __('Employee Course List') }}</h3>
        <span class="branch-label-tag">{{ __('In Progress') }}</span>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>{{ __('Course') }}</th>
                <th>{{ __('Classification') }}</th>
                <th>{{ __('Deadline') }}</th>
                <th>{{ __('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($enrollments as $enr)
            @php
                $totalTodos = $enr->course->todos->count();
                $completedTodos = $enr->todoResponses->count();
                $hasFailed = $enr->todoResponses->where('status', 'FAILED')->isNotEmpty();
            @endphp
            <tr class="cursor-pointer hover:bg-slate-50 dark:hover:bg-navy-700/50" onclick="window.location='{{ route('attendance.show', $enr->course_id) }}'">
                <td class="font-medium">{{ $enr->course->course_name }}</td>
                <td>{{ $enr->course->categoryDetail->detail_name ?? '-' }}</td>
                <td>{{ $enr->enrollment_deadline }}</td>
                <td>
                    @if($completedTodos === 0)
                        <span class="status-badge status-badge-pending">{{ __('Pending') }}</span>
                    @elseif($completedTodos < $totalTodos || $hasFailed)
                        <span class="status-badge status-badge-in-progress">{{ __('In Progress') }}</span>
                    @else
                        <span class="status-badge status-badge-completed">{{ __('Completed') }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-slate-400 py-8">{{ __('No data available') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $enrollments->links() }}</div>
@endsection
