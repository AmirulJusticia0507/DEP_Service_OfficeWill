@extends('layouts.app')
@section('title', $course->course_name)
@section('header-icon')<i class="ti ti-calendar-check"></i>@endsection
@section('breadcrumbs')
    <span class="text-slate-800 dark:text-slate-100 font-medium">{{ __('Attendance') }}</span>
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">{{ $course->course_name }}</span>
@endsection
@section('content')
<div class="bg-white rounded shadow mb-6 overflow-hidden dark:bg-navy-800">
    <div class="bg-primary text-white p-4">
        <h1 class="text-lg font-bold">{{ $course->course_name }}</h1>
        <p class="text-xs text-sidebar-accent-dim mt-0.5">{{ $course->categoryDetail->detail_name ?? '-' }}</p>
    </div>
    <div class="p-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div>
            <span class="text-xs text-slate-400">{{ __('Deadline') }}</span>
            <p class="font-medium">{{ $enrollment->enrollment_deadline }}</p>
        </div>
        <div>
            <span class="text-xs text-slate-400">{{ __('Status') }}</span>
            <p>@include('components.status-badge', ['status' => $enrollment->status])</p>
        </div>
        <div>
            <span class="text-xs text-slate-400">Progress</span>
            <p class="font-medium">
                @php
                    $total = $course->todos->count();
                    $done = $enrollment->todoResponses->count();
                @endphp
                {{ $done }}/{{ $total }}
            </p>
        </div>
        <div>
            <span class="text-xs text-slate-400">Soal</span>
            <p class="font-medium">{{ $course->questions->count() }} soal</p>
        </div>
    </div>
</div>

@if($course->materials->isNotEmpty())
<div class="bg-white rounded shadow mb-6 dark:bg-navy-800">
    <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200"><i class="ti ti-book text-accent mr-1"></i> {{ __('Materials') }}</h3>
    </div>
    <div class="p-4 space-y-2">
        @foreach($course->materials as $mat)
        <div class="flex items-center gap-3 py-2 px-3 rounded bg-slate-50 dark:bg-navy-900/50">
            <span class="status-badge {{ $mat->material_type === 'YOUTUBE' ? 'status-badge-in-progress' : 'status-badge-completed' }}">{{ $mat->material_type }}</span>
            <span class="text-sm font-medium flex-1">{{ $mat->title }}</span>
            <a href="{{ $mat->content_url_or_path }}" target="_blank" class="btn-primary text-xs">{{ __('View Materials') }}</a>
        </div>
        @endforeach
    </div>
</div>
@endif

@if($course->todos->isNotEmpty())
<div class="bg-white rounded shadow mb-6 dark:bg-navy-800">
    <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200"><i class="ti ti-checklist text-accent mr-1"></i> {{ __('Post-Course ToDo') }}</h3>
    </div>
    <div class="p-4 space-y-2">
        @foreach($course->todos as $todo)
        @php
            $response = $enrollment->todoResponses->where('course_todo_id', $todo->id)->first();
            $attempt = \App\Models\ExamAttempt::where('enrollment_id', $enrollment->id)
                ->where('course_todo_id', $todo->id)->latest()->first();
        @endphp
        <div class="flex items-center gap-3 py-2 px-3 rounded {{ $loop->even ? 'bg-slate-50 dark:bg-navy-900/50' : '' }}">
            <span class="status-badge 
                @if($response && $response->status === 'PASSED') status-badge-completed
                @elseif($response && $response->status === 'FAILED') status-badge-pending
                @else status-badge-in-progress @endif">
                @if($response && $response->status === 'PASSED') {{ __('Completed') }}
                @elseif($response && $response->status === 'FAILED') {{ __('Fail') }}
                @else {{ __('Pending') }} @endif
            </span>
            <span class="text-sm font-medium flex-1">{{ $todo->title }}</span>
            @if($todo->todo_type === 'TEST' && $course->questions->isNotEmpty())
                @if(!$response || $response->status !== 'PASSED')
                <a href="{{ route('exam.start', [$enrollment, $todo]) }}" class="btn-gold text-xs">
                    <i class="ti ti-player-play"></i> {{ __('Start Exam') }}
                </a>
                @endif
            @else
                <a href="{{ route('attendance.todos', $enrollment) }}" class="btn-primary text-xs">{{ __('Go to ToDo') }}</a>
            @endif
            @if($attempt)
                <span class="text-[10px] text-slate-400">#{{ $attempt->attempt_number }} {{ $attempt->total_score }}/{{ $attempt->max_score }}</span>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

<div class="flex items-center justify-end gap-2">
    @if($enrollment->status === 'ENROLLED')
    <a href="{{ route('attendance.todos', $enrollment) }}" class="btn-primary"><i class="ti ti-checklist"></i> {{ __('Go to ToDo') }}</a>
    @endif
    @if($enrollment->status === 'COMPLETED')
    <a href="{{ route('attendance.score', $enrollment) }}" class="btn-primary"><i class="ti ti-score"></i> {{ __('View Score') }}</a>
    @endif
</div>
@endsection
