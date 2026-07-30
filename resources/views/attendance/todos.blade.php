@extends('layouts.app')
@section('title', 'Post-Course ToDo')
@section('header-icon')
    <i class="ti ti-calendar-check"></i>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 dark:text-slate-100 font-medium">Attendance</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">{{ $enrollment->course->course_name }}</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">ToDo</span>
@endsection
@section('content')
<h2 class="text-lg font-bold text-primary mb-4">Post-Course ToDo</h2>

<x-form-section-header>Course Information</x-form-section-header>
<div class="bg-white dark:bg-navy-800 shadow rounded mb-6 p-4 grid grid-cols-2 gap-4 text-sm">
    <div><span class="text-slate-500 dark:text-slate-300">Course:</span> <span class="font-medium">{{ $enrollment->course->course_name }}</span></div>
    <div><span class="text-slate-500 dark:text-slate-300">Deadline:</span> <span class="font-medium">{{ $enrollment->enrollment_deadline }}</span></div>
    <div><span class="text-slate-500 dark:text-slate-300">Status:</span>
        @if($enrollment->status === 'ENROLLED')
            <x-status-badge status="ENROLLED">回答中</x-status-badge>
        @elseif($enrollment->status === 'COMPLETED')
            <x-status-badge status="COMPLETED">修了</x-status-badge>
        @endif
    </div>
</div>

<div class="space-y-6">
    @forelse ($enrollment->course->todos as $todo)
    @php $response = $enrollment->todoResponses->where('course_todo_id', $todo->id)->first(); @endphp

    <div class="bg-white dark:bg-navy-800 shadow rounded">
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200 dark:border-b dark:border-slate-700">
            <div class="flex items-center gap-2">
                <span class="status-badge
                    @if($todo->todo_type === 'QUESTIONNAIRE') status-badge-pending
                    @elseif($todo->todo_type === 'REPORT') status-badge-in-progress
                    @else status-badge-completed @endif">{{ $todo->todo_type }}</span>
                <strong class="text-sm">{{ $todo->title }}</strong>
            </div>
            @if($response && $response->status === 'PASSED')
                <span class="status-badge status-badge-completed">修了</span>
            @elseif($response && $response->status === 'FAILED')
                <span class="text-rose-500 text-xs font-medium">✗ Tidak lulus ({{ $response->score }})</span>
            @endif
        </div>

        <div class="p-4">
            @if($todo->description)
                <p class="text-xs text-slate-500 dark:text-slate-300 mb-3">{{ $todo->description }}</p>
            @endif

            @if($todo->todo_type === 'QUESTIONNAIRE')
                @if($response)
                    <div class="bg-slate-50 dark:bg-navy-900/50 rounded p-3 text-sm">{{ $response->response_content }}</div>
                @else
                    <form method="POST" action="{{ route('todos.questionnaire', $todo) }}">
                        @csrf
                        <textarea name="response_content" rows="4" placeholder="Tulis jawaban Anda di sini..." class="form-input"></textarea>
                        <button type="submit" class="btn-primary mt-2">Answer the survey</button>
                    </form>
                @endif

            @elseif($todo->todo_type === 'TEST')
                @if($response)
                    <div class="bg-slate-50 dark:bg-navy-900/50 rounded p-3 text-sm">
                        <span class="font-medium">Score:</span> {{ $response->score }}
                        @if($response->status === 'PASSED')
                            <span class="status-badge status-badge-completed ml-2">{{ __('Pass') }}</span>
                        @else
                            <span class="text-rose-500 ml-2">{{ __('Fail') }}</span>
                        @endif
                    </div>
                    @if($response->status !== 'PASSED' && $enrollment->course->questions->isNotEmpty())
                    <a href="{{ route('exam.start', [$enrollment, $todo]) }}" class="btn-gold text-xs mt-2 inline-flex items-center gap-1">
                        <i class="ti ti-player-play"></i> {{ __('Start Exam') }}
                    </a>
                    @endif
                @else
                    @if($enrollment->course->questions->isNotEmpty())
                    <a href="{{ route('exam.start', [$enrollment, $todo]) }}" class="btn-gold text-xs inline-flex items-center gap-1">
                        <i class="ti ti-player-play"></i> {{ __('Start Exam') }}
                    </a>
                    @else
                    <form method="POST" action="{{ route('todos.test', $todo) }}" class="flex gap-2 items-end">
                        @csrf
                        <div>
                            <label class="text-xs text-slate-500 dark:text-slate-300 block mb-0.5">Score (0-100)</label>
                            <input type="number" name="score" min="0" max="100" required class="form-input w-24">
                        </div>
                        <button type="submit" class="btn-primary">{{ __('Score') }}</button>
                    </form>
                    @endif
                @endif

            @elseif($todo->todo_type === 'REPORT')
                @if($response)
                    <div class="bg-slate-50 dark:bg-navy-900/50 rounded p-3 text-sm">
                        <span class="text-green-600">✓ Laporan sudah dikirim</span>
                        @if($response->response_content)
                            <a href="{{ asset('storage/' . $response->response_content) }}" target="_blank" class="text-primary hover:underline ml-2 text-xs">Lihat file</a>
                        @endif
                    </div>
                @else
                    <form method="POST" action="{{ route('todos.report', $todo) }}" enctype="multipart/form-data">
                        @csrf
                        <textarea name="response_content" rows="4" placeholder="Tulis laporan Anda di sini..." class="form-input mb-2"></textarea>
                        <div class="flex gap-2 items-end">
                            <div>
                                <label class="text-xs text-slate-500 dark:text-slate-300 block mb-0.5">Atau upload file</label>
                                <input type="file" name="report_file" class="text-xs">
                            </div>
                            <button type="submit" name="action" value="save" class="btn-secondary">Temporarily save</button>
                            <button type="submit" name="action" value="submit" class="btn-primary">Submit a report</button>
                        </div>
                    </form>
                @endif
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white dark:bg-navy-800 shadow rounded p-6 text-center text-slate-400 dark:text-slate-500">Tidak ada todo.</div>
    @endforelse
</div>

<div class="mt-6 flex justify-end gap-2">
    <a href="{{ route('attendance.show', $enrollment->course_id) }}" class="btn-secondary">Kembali</a>
    @if($enrollment->status === 'ENROLLED')
    <form method="POST" action="{{ route('attendance.complete', $enrollment) }}">
        @csrf
        <button type="submit" class="btn-primary">Complete</button>
    </form>
    @endif
</div>
@endsection
