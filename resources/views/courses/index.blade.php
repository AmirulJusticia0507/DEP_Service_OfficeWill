@extends('layouts.app')
@section('title', 'Daftar Kursus')
@section('header-icon')<i class="ti ti-book"></i>@endsection
@section('breadcrumbs')
    <span class="text-slate-800 font-medium dark:text-slate-100">{{ __('Courses') }}</span>
@endsection
@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-bold text-accent">{{ __('Courses') }}</h2>
    <a href="{{ route('courses.create') }}" class="btn-primary flex items-center gap-1"><i class="ti ti-plus"></i> {{ __('Add New') }}</a>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th class="w-14"></th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Category') }}</th>
            <th>{{ __('Score') }}</th>
            <th>Retest</th>
            <th>{{ __('Questions') }}</th>
            <th>{{ __('Actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($courses as $course)
        <tr>
            <td class="w-14">
                @if ($course->photo)
                    <img src="{{ Storage::url($course->photo) }}" alt="" class="w-12 h-9 object-cover rounded border border-slate-200 dark:border-slate-700">
                @else
                    <div class="w-12 h-9 bg-slate-100 dark:bg-navy-900 rounded border flex items-center justify-center text-slate-300 text-xs">No</div>
                @endif
            </td>
            <td class="font-medium">{{ $course->course_name }}</td>
            <td>{{ $course->categoryDetail->detail_name ?? '-' }}</td>
            <td>{{ $course->passing_score }}</td>
            <td class="text-center">{{ $course->has_retest ? 'Ya' : '-' }}</td>
            <td>
                <a href="{{ route('admin.questions.index', $course) }}" class="text-accent hover:underline text-xs font-medium flex items-center gap-1">
                    <i class="ti ti-question-mark"></i> {{ $course->questions_count ?? 0 }} soal
                </a>
            </td>
            <td>
                <div class="flex items-center gap-1">
                    <a href="{{ route('courses.edit', $course) }}" class="icon-btn" title="{{ __('Edit') }}"><i class="ti ti-pencil text-xs"></i></a>
                    <a href="{{ route('admin.questions.index', $course) }}" class="icon-btn" title="{{ __('Question Bank') }}"><i class="ti ti-question-mark text-xs"></i></a>
                    <form method="POST" action="{{ route('courses.destroy', $course) }}" onsubmit="return confirm('{{ __('Confirm Delete') }}')">
                        @csrf @method('DELETE')
                        <button class="icon-btn text-rose-500" title="{{ __('Delete') }}"><i class="ti ti-trash text-xs"></i></button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-slate-400 py-8">{{ __('No data available') }}</td></tr>
        @endforelse
    </tbody>
</table>

<div class="mt-4">{{ $courses->links() }}</div>
@endsection
