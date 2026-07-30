@extends('layouts.app')
@section('title', 'Attendance Settings')
@section('header-icon')
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 dark:text-slate-100 font-medium">Course Settings</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">Attendance Settings</span>
@endsection
@section('content')
<h2 class="text-lg font-bold text-[#1e3a8a] mb-4">Attendance Settings — Assign Course to Employees</h2>

<div class="bg-white dark:bg-navy-800 shadow rounded max-w-3xl">
    {{-- Step Indicator --}}
    <div class="flex border-b border-slate-200 dark:border-b dark:border-slate-700">
        <div class="flex-1 text-center py-3 bg-[#1e3a8a] text-white text-sm font-medium">Step 1: Select Course</div>
        <div class="flex-1 text-center py-3 bg-[#bfdbfe] text-[#1e3a8a] text-sm font-medium">Step 2: Select Employees</div>
    </div>

    <form method="POST">
        @csrf

        <x-form-section-header>Step 1: Target Course</x-form-section-header>
        <div class="p-4">
            <label class="block text-sm mb-1"><x-required-mark /> Course</label>
            <select name="course_id" class="form-select max-w-md">
                @foreach ($courses as $c)
                    <option value="{{ $c->id }}">{{ $c->course_name }}</option>
                @endforeach
            </select>
        </div>

        <x-form-section-header>Step 2: Target Employees</x-form-section-header>
        <div class="p-4 space-y-4">
            <div>
                <label class="block text-sm mb-1"><x-required-mark /> Employees</label>
                <div class="max-h-64 overflow-y-auto border border-slate-200 dark:border-slate-700 rounded p-2 space-y-1">
                    @foreach ($employees as $emp)
                    <label class="flex items-center gap-2 text-sm py-0.5 hover:bg-slate-50 dark:hover:bg-navy-700/50 px-1 rounded">
                        <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" class="rounded border-slate-300 dark:border-slate-600 dark:bg-navy-800">
                        {{ $emp->full_name }}
                        <span class="text-xs text-slate-400 dark:text-slate-500">({{ $emp->employee_code }})</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-sm mb-1"><x-required-mark /> Enrollment Deadline</label>
                <input type="date" name="enrollment_deadline" required class="form-input w-48">
            </div>
        </div>

        <x-action-buttons align="left" class="p-4 border-t border-slate-200 dark:border-t dark:border-slate-700">
            <button type="submit" class="btn-primary">Assign Course to Employees</button>
            <a href="{{ route('admin.assignments.index') }}" class="btn-secondary">Cancel</a>
        </x-action-buttons>
    </form>
</div>
@endsection
