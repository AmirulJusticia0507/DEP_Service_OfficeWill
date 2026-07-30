@extends('layouts.app')
@section('title', 'Question Bank - ' . $course->course_name)
@section('header-icon')<i class="ti ti-question-mark"></i>@endsection
@section('breadcrumbs')
    <a href="{{ route('courses.index') }}" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">{{ __('Courses') }}</a>
    <span class="mx-1">/</span>
    <span class="text-slate-800 font-medium dark:text-slate-100">{{ __('Questions') }} — {{ $course->course_name }}</span>
@endsection
@section('content')
<div class="flex items-center justify-between mb-4">
    <p class="text-sm text-slate-500 dark:text-slate-400">Total: {{ $course->questions->count() }} soal</p>
    <a href="{{ route('admin.questions.create', $course) }}" class="btn-primary flex items-center gap-1"><i class="ti ti-plus text-sm"></i> {{ __('Add New') }}</a>
</div>

@if($course->questions->isEmpty())
    <div class="bg-white rounded shadow p-8 text-center text-slate-400 dark:bg-navy-800 dark:text-slate-500">{{ __('No data available') }}</div>
@else
    <div class="space-y-3">
        @foreach($course->questions as $q)
        <div class="bg-white rounded shadow p-4 dark:bg-navy-800">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded 
                            @if($q->question_type === 'MCQ') bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300
                            @elseif($q->question_type === 'TRUE_FALSE') bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300
                            @else bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300 @endif">
                            {{ $q->question_type }}
                        </span>
                        <span class="text-xs text-slate-400">{{ $q->points }} pts</span>
                        <span class="text-xs text-slate-400">#{{ $q->display_order }}</span>
                    </div>
                    <p class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $q->question_text }}</p>
                    @if(in_array($q->question_type, ['MCQ', 'TRUE_FALSE']))
                    <ul class="mt-2 space-y-0.5">
                        @foreach($q->options as $opt)
                        <li class="text-xs {{ $opt->is_correct ? 'text-emerald-600 dark:text-emerald-400 font-semibold' : 'text-slate-500 dark:text-slate-400' }}">
                            {{ $opt->is_correct ? '✓' : '○' }} {{ $opt->option_text }}
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                <div class="flex items-center gap-1 ml-3">
                    <a href="{{ route('admin.questions.edit', [$course, $q]) }}" class="icon-btn" title="{{ __('Edit') }}"><i class="ti ti-pencil text-xs"></i></a>
                    <form method="POST" action="{{ route('admin.questions.destroy', [$course, $q]) }}" onsubmit="return confirm('{{ __('Confirm Delete') }}')">
                        @csrf @method('DELETE')
                        <button class="icon-btn text-rose-500" title="{{ __('Delete') }}"><i class="ti ti-trash text-xs"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
