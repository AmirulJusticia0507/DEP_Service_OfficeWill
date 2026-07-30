@extends('layouts.app')
@section('title', 'Test Result')
@section('header-icon')
    <i class="ti ti-calendar-check"></i>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 dark:text-slate-100 font-medium">Attendance</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">{{ $enrollment->course->course_name }}</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">Scoring Result</span>
@endsection
@section('content')
<h2 class="text-lg font-bold text-[#1e3a8a] mb-4">Post-Course Test Scoring Result</h2>

@php
    $testTodos = $enrollment->course->todos->where('todo_type', 'TEST');
    $totalQuestions = $testTodos->count();
    $correctQuestions = $enrollment->todoResponses->where('course_todo_id', $testTodos->pluck('id')->toArray())->where('status', 'PASSED')->count();
    $passed = $enrollment->todoResponses->where('status', 'FAILED')->isEmpty();
    $scorePercent = $totalQuestions > 0 ? round(($correctQuestions / $totalQuestions) * 100) : 0;
@endphp

<div class="bg-white dark:bg-navy-800 shadow rounded mb-6 p-6 text-center">
    <p class="text-xs text-slate-500 dark:text-slate-300 uppercase tracking-wide mb-1">Test Result</p>
    <p class="text-3xl font-bold {{ $passed ? 'text-emerald-600' : 'text-[#dc2626]' }}">{{ $passed ? 'PASS' : 'FAIL' }}</p>
    <div class="flex justify-center gap-8 mt-4 text-sm">
        <div><span class="text-slate-500 dark:text-slate-300">Total Questions:</span> <span class="font-semibold">{{ $totalQuestions }}</span></div>
        <div><span class="text-slate-500 dark:text-slate-300">Correct:</span> <span class="font-semibold text-emerald-600">{{ $correctQuestions }}</span></div>
        <div><span class="text-slate-500 dark:text-slate-300">Score:</span> <span class="font-semibold">{{ $scorePercent }}%</span></div>
    </div>
</div>

<div class="space-y-4">
    @foreach ($enrollment->course->todos as $todo)
    @php $response = $enrollment->todoResponses->where('course_todo_id', $todo->id)->first(); @endphp
    <div class="bg-white dark:bg-navy-800 shadow rounded p-4">
        <div class="flex items-center gap-2 mb-2">
            <span class="status-badge status-badge-completed">TEST</span>
            <strong class="text-sm">{{ $todo->title }}</strong>
        </div>
        @if($todo->description)
            <p class="text-xs text-slate-500 dark:text-slate-300 mb-2">{{ $todo->description }}</p>
        @endif
        <div class="text-sm space-y-1">
            <p><span class="text-slate-500 dark:text-slate-300">Your score:</span> <span class="font-semibold">{{ $response?->score ?? '-' }}</span></p>
            @if($response)
            <p>
                <span class="text-slate-500 dark:text-slate-300">Status:</span>
                @if($response->status === 'PASSED')
                    <span class="status-badge status-badge-completed">Lulus</span>
                @else
                    <span class="text-[#dc2626] font-medium">Tidak lulus</span>
                @endif
            </p>
            @endif
        </div>
    </div>
    @endforeach
</div>

<div class="mt-6 flex justify-end gap-2">
    <a href="{{ route('attendance.todos', $enrollment) }}" class="btn-secondary">Back to ToDo</a>
    @if(!$passed && $enrollment->course->has_retest)
        <a href="{{ route('attendance.todos', $enrollment) }}" class="btn-primary">Retest</a>
    @endif
    @if($passed)
        <form method="POST" action="{{ route('attendance.complete', $enrollment) }}">
            @csrf
            <button type="submit" class="btn-primary">Complete</button>
        </form>
    @endif
</div>
@endsection
