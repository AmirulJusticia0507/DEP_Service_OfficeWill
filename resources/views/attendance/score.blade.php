@extends('layouts.app')
@section('title', 'Test Result')
@section('header-icon')<i class="ti ti-score"></i>@endsection
@section('breadcrumbs')
    <span class="text-slate-800 font-medium dark:text-slate-100">{{ __('Test Score') }} — {{ $enrollment->course->course_name }}</span>
@endsection
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded shadow mb-6 p-6 text-center dark:bg-navy-800"
         style="border-top: 4px solid {{ $enrollment->todoResponses->where('status', 'FAILED')->isEmpty() ? '#10b981' : '#dc2626' }}">
        <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">{{ __('Test Score') }}</p>
        @php
            $allPassed = $enrollment->todoResponses->where('status', 'FAILED')->isEmpty();
        @endphp
        <p class="text-4xl font-bold {{ $allPassed ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
            {{ $allPassed ? __('Pass') : __('Fail') }}
        </p>
    </div>

    <div class="space-y-4">
        @foreach ($enrollment->course->todos as $todo)
        @php
            $response = $enrollment->todoResponses->where('course_todo_id', $todo->id)->first();
            $attempt = isset($attempts) ? ($attempts[$todo->id] ?? null) : null;
        @endphp
        <div class="bg-white rounded shadow p-5 dark:bg-navy-800">
            <div class="flex items-center gap-2 mb-3">
                <span class="status-badge 
                    @if($todo->todo_type === 'TEST') status-badge-in-progress
                    @elseif($todo->todo_type === 'QUESTIONNAIRE') status-badge-completed
                    @else status-badge-pending @endif">
                    {{ $todo->todo_type }}
                </span>
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $todo->title }}</h3>
            </div>

            @if($response)
            <div class="flex items-center gap-4 text-sm">
                <span class="text-slate-400">{{ __('Score') }}:</span>
                <span class="font-semibold text-lg">{{ $response->score ?? '-' }}</span>
                <span class="ml-2">
                    @if($response->status === 'PASSED')
                        <span class="status-badge status-badge-completed">{{ __('Pass') }}</span>
                    @else
                        <span class="status-badge status-badge-pending">{{ __('Fail') }}</span>
                    @endif
                </span>
            </div>
            @endif

            @if($attempt && $attempt->answers->isNotEmpty())
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700">
                <p class="text-xs text-slate-400 mb-2">Attempt #{{ $attempt->attempt_number }} — {{ $attempt->total_score }}/{{ $attempt->max_score }}</p>
                <div class="space-y-2">
                    @foreach($attempt->answers as $answer)
                    <div class="flex items-start gap-2 text-xs p-2 rounded {{ $answer->is_correct === true ? 'bg-emerald-50 dark:bg-emerald-900/20' : ($answer->is_correct === false ? 'bg-rose-50 dark:bg-rose-900/20' : 'bg-slate-50 dark:bg-navy-900/50') }}">
                        <span class="shrink-0 mt-0.5">
                            @if($answer->is_correct === true) 
                                <i class="ti ti-check text-emerald-500"></i>
                            @elseif($answer->is_correct === false)
                                <i class="ti ti-x text-rose-500"></i>
                            @else
                                <i class="ti ti-minus text-slate-400"></i>
                            @endif
                        </span>
                        <div class="flex-1">
                            <p class="font-medium">{{ $answer->question->question_text }}</p>
                            @if($answer->selectedOption)
                            <p class="text-slate-400">{{ __('Your Answer') }}: {{ $answer->selectedOption->option_text }}</p>
                            @endif
                            @if($answer->essay_answer)
                            <p class="text-slate-400">{{ __('Your Answer') }}: {{ \Illuminate\Support\Str::limit($answer->essay_answer, 80) }}</p>
                            @endif
                            @if($answer->is_correct === false && $answer->question->correctOption)
                            <p class="text-emerald-600 dark:text-emerald-400">{{ __('Correct Answer') }}: {{ $answer->question->correctOption->option_text }}</p>
                            @endif
                            <p class="text-slate-400">+{{ $answer->points_earned }} pts</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <div class="mt-6 flex items-center justify-end gap-2">
        <a href="{{ route('attendance.show', $enrollment->course) }}" class="btn-secondary">{{ __('Back to List') }}</a>
        @if(!$allPassed && $enrollment->course->has_retest)
            <a href="{{ route('attendance.todos', $enrollment) }}" class="btn-primary">Retest</a>
        @endif
        @if($allPassed && $enrollment->status === 'ENROLLED')
            <form method="POST" action="{{ route('attendance.complete', $enrollment) }}">
                @csrf
                <button type="submit" class="btn-gold"><i class="ti ti-certificate"></i> {{ __('Complete') }}</button>
            </form>
        @endif
    </div>
</div>
@endsection
