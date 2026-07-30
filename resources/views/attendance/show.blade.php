@extends('layouts.app')
@section('title', $course->course_name)
@section('header-icon')
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 dark:text-slate-100 font-medium">Attendance</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">{{ $course->course_name }}</span>
@endsection
@section('content')
<h2 class="text-lg font-bold text-[#1e3a8a] mb-4">Course Attendance</h2>

<x-form-section-header>Course Information</x-form-section-header>
<div class="bg-white dark:bg-navy-800 shadow rounded mb-6 p-4 grid grid-cols-2 gap-4 text-sm">
    <div><span class="text-slate-500 dark:text-slate-300">Course Name:</span> <span class="font-medium">{{ $course->course_name }}</span></div>
    <div><span class="text-slate-500 dark:text-slate-300">Classification:</span> <span class="font-medium">{{ $course->categoryDetail->detail_name ?? '-' }}</span></div>
    <div><span class="text-slate-500 dark:text-slate-300">Enrollment Deadline:</span> <span class="font-medium">{{ $enrollment->enrollment_deadline }}</span></div>
    <div><span class="text-slate-500 dark:text-slate-300">Status:</span>
        @if($enrollment->status === 'ENROLLED')
            <x-status-badge status="ENROLLED">回答中</x-status-badge>
        @elseif($enrollment->status === 'COMPLETED')
            <x-status-badge status="COMPLETED">修了</x-status-badge>
        @endif
    </div>
</div>

<x-form-section-header>Materials</x-form-section-header>
<div class="bg-white dark:bg-navy-800 shadow rounded mb-6 p-4 space-y-2">
    @forelse ($course->materials as $mat)
    <div class="flex items-center gap-3 py-2 px-3 rounded {{ $loop->even ? 'bg-slate-50 dark:bg-navy-900/50' : '' }}">
        <span class="status-badge {{ $mat->material_type === 'YOUTUBE' ? 'status-badge-in-progress' : 'status-badge-completed' }}">{{ $mat->material_type }}</span>
        <span class="text-sm font-medium">{{ $mat->title }}</span>
        <a href="{{ $mat->content_url_or_path }}" target="_blank" class="btn-primary text-xs ml-auto">View materials</a>
    </div>
    @empty
    <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada materi.</p>
    @endforelse
</div>

<div class="flex justify-end gap-2">
    @if($enrollment->status === 'ENROLLED')
    <a href="{{ route('attendance.todos', $enrollment) }}" class="btn-primary">Go to ToDo</a>
    @endif
    @if($enrollment->status === 'COMPLETED')
    <a href="{{ route('attendance.score', $enrollment) }}" class="btn-primary">View Score</a>
    @endif
</div>
@endsection
