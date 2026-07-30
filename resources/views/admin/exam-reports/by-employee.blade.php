@extends('layouts.app')
@section('title', 'Exam Report by Employee')
@section('header-icon')<i class="ti ti-report-analytics"></i>@endsection
@section('breadcrumbs')
    <span class="text-slate-800 font-medium dark:text-slate-100">{{ __('Exam Report') }} — {{ __('By Employee') }}</span>
@endsection
@section('content')
<div class="bg-white rounded shadow p-4 mb-6 dark:bg-navy-800">
    <form method="GET" class="flex items-end gap-3">
        <div class="flex-1">
            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Employee') }}</label>
            <select name="employee_id" class="form-select" onchange="this.form.submit()">
                <option value="">-- {{ __('Please select') }} --</option>
                @foreach($employees as $e)
                <option value="{{ $e->id }}" @selected($selectedEmployee && $selectedEmployee->id === $e->id)>{{ $e->full_name }} ({{ $e->employee_code }})</option>
                @endforeach
            </select>
        </div>
        @if($selectedEmployee)
        <a href="{{ route('admin.exam-reports.by-employee') }}" class="btn-secondary text-xs">{{ __('Reset') }}</a>
        @endif
    </form>
</div>

@if($selectedEmployee && $attempts->isNotEmpty())
    <div class="space-y-4">
        @foreach($attempts as $courseName => $courseAttempts)
        <div class="bg-white rounded shadow dark:bg-navy-800">
            <div class="px-4 py-3 bg-maroon-700 text-white rounded-t flex items-center justify-between">
                <h3 class="text-sm font-semibold">{{ $courseName }}</h3>
                <span class="text-xs text-gold-300">Attempts: {{ $courseAttempts->count() }}</span>
            </div>
            <div class="p-4">
                @foreach($courseAttempts as $attempt)
                <div class="mb-3 pb-3 {{ !$loop->last ? 'border-b border-slate-100 dark:border-slate-700' : '' }}">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="font-medium">Attempt #{{ $attempt->attempt_number }}</span>
                        <span class="text-xs {{ $attempt->total_score >= ($attempt->courseTodo->passing_score ?? $attempt->max_score / 2) ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $attempt->total_score }}/{{ $attempt->max_score }}
                            ({{ $attempt->max_score > 0 ? round(($attempt->total_score / $attempt->max_score) * 100) : 0 }}%)
                        </span>
                    </div>
                    <div class="space-y-1">
                        @foreach($attempt->answers as $answer)
                        <div class="flex items-center gap-2 text-xs p-1.5 rounded {{ $answer->is_correct === true ? 'bg-emerald-50 dark:bg-emerald-900/20' : ($answer->is_correct === false ? 'bg-rose-50 dark:bg-rose-900/20' : 'bg-slate-50 dark:bg-navy-900/50') }}">
                            <span class="shrink-0 w-4 text-center">
                                @if($answer->is_correct === true) <i class="ti ti-check text-emerald-500"></i>
                                @elseif($answer->is_correct === false) <i class="ti ti-x text-rose-500"></i>
                                @else <i class="ti ti-minus text-slate-400"></i> @endif
                            </span>
                            <span class="flex-1 truncate">{{ \Illuminate\Support\Str::limit($answer->question->question_text, 60) }}</span>
                            <span class="text-slate-400">+{{ $answer->points_earned }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
@elseif($selectedEmployee)
    <div class="bg-white rounded shadow p-8 text-center text-slate-400 dark:bg-navy-800">{{ __('No data available') }}</div>
@endif
@endsection
