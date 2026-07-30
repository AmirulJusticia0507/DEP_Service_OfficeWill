@extends('layouts.app')
@section('title', 'Edit Kursus')
@section('header-icon')
    <i class="ti ti-book"></i>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 dark:text-slate-100 font-medium">In-house Training</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">Course List</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">Course Edit</span>
@endsection
@section('content')
<h2 class="text-lg font-bold text-accent mb-4">{{ __('Edit') }}: {{ $course->course_name }}</h2>

<div class="mb-4 flex items-center gap-2">
    <a href="{{ route('admin.questions.index', $course) }}" class="btn-gold text-xs flex items-center gap-1"><i class="ti ti-question-mark"></i> {{ __('Question Bank') }} ({{ $course->questions_count ?? 0 }})</a>
</div>

<form method="POST" enctype="multipart/form-data" class="bg-white dark:bg-navy-800 shadow rounded max-w-2xl mb-8">
    @csrf
    @method('PUT')

    <x-form-section-header>Course Information</x-form-section-header>
    <div class="p-4 space-y-3">
        <div>
            <label class="block text-sm mb-1"><x-required-mark /> Kategori Detail</label>
            <select name="category_detail_id" class="form-select">
                @foreach ($categories as $cat)
                    <optgroup label="{{ $cat->category_name }}">
                        @foreach ($cat->details as $det)
                            <option value="{{ $det->id }}" @selected($det->id === $course->category_detail_id)>{{ $det->detail_name }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1"><x-required-mark /> Nama Kursus</label>
            <input type="text" name="course_name" value="{{ $course->course_name }}" required class="form-input">
        </div>
        <div>
            <label class="block text-sm mb-1">Deskripsi</label>
            <textarea name="description" rows="3" class="form-input">{{ $course->description }}</textarea>
        </div>
        <div>
            <label class="block text-sm mb-1">Foto Kursus</label>
            @if ($course->photo)
                <div class="mb-2">
                    <img src="{{ Storage::url($course->photo) }}" alt="{{ $course->course_name }}" class="w-40 h-28 object-cover rounded border border-slate-200 dark:border-slate-700">
                </div>
            @endif
            <input type="file" name="photo" accept="image/jpeg,image/png,image/gif,image/webp" class="form-input">
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Format: JPEG, PNG, GIF, WebP. Maks 2MB. Kosongkan jika tidak ingin mengubah.</p>
        </div>
        <div class="flex gap-6 items-end">
            <div>
                <label class="block text-sm mb-1">Passing Score</label>
                <input type="number" name="passing_score" value="{{ $course->passing_score }}" class="form-input w-24">
            </div>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="has_retest" value="1" @checked($course->has_retest) class="rounded border-slate-300 dark:border-slate-600 dark:bg-navy-800"> Ada Retest</label>
        </div>
    </div>

    <x-action-buttons align="left" class="p-4 border-t border-slate-200 dark:border-t dark:border-slate-700">
        <button type="submit" class="btn-primary">Simpan</button>
        <a href="{{ route('courses.index') }}" class="btn-secondary">Kembali</a>
    </x-action-buttons>
</form>

<x-form-section-header>Course Materials</x-form-section-header>
<div class="bg-white dark:bg-navy-800 shadow rounded mb-6">
    <div class="p-4">
        <form method="POST" action="{{ route('courses.materials.store', $course) }}" class="flex flex-wrap gap-2 mb-4 items-end">
            @csrf
            <div>
                <label class="text-xs text-slate-500 dark:text-slate-300 block mb-0.5">Type</label>
                <select name="material_type" class="form-select w-28">
                    <option value="YOUTUBE">YouTube</option>
                    <option value="PDF">PDF</option>
                </select>
            </div>
            <div>
                <label class="text-xs text-slate-500 dark:text-slate-300 block mb-0.5">Title</label>
                <input type="text" name="title" placeholder="Judul" required class="form-input w-48">
            </div>
            <div>
                <label class="text-xs text-slate-500 dark:text-slate-300 block mb-0.5">URL / Path</label>
                <input type="text" name="content_url_or_path" placeholder="URL" required class="form-input w-48">
            </div>
            <div>
                <label class="text-xs text-slate-500 dark:text-slate-300 block mb-0.5">Order</label>
                <input type="number" name="display_order" placeholder="Urutan" class="form-input w-16">
            </div>
            <button class="btn-primary">Tambah</button>
        </form>

        <div class="space-y-1">
            @forelse ($course->materials as $mat)
                <div class="flex items-center gap-3 py-2 px-3 rounded {{ $loop->even ? 'bg-slate-50 dark:bg-navy-900/50' : '' }}">
                    <span class="status-badge {{ $mat->material_type === 'YOUTUBE' ? 'status-badge-in-progress' : 'status-badge-completed' }}">{{ $mat->material_type }}</span>
                    <span class="text-sm font-medium">{{ $mat->title }}</span>
                    <span class="text-xs text-slate-400 dark:text-slate-500">— {{ $mat->content_url_or_path }}</span>
                    <form method="POST" action="{{ route('materials.destroy', $mat) }}" class="ml-auto">
                        @csrf @method('DELETE')
                        <button class="text-[#dc2626] hover:underline text-xs">Hapus</button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada materi.</p>
            @endforelse
        </div>
    </div>
</div>

<x-form-section-header>Course Todo</x-form-section-header>
<div class="bg-white dark:bg-navy-800 shadow rounded">
    <div class="p-4">
        <form method="POST" action="{{ route('courses.todos.store', $course) }}" class="flex flex-wrap gap-2 mb-4 items-end">
            @csrf
            <div>
                <label class="text-xs text-slate-500 dark:text-slate-300 block mb-0.5">Type</label>
                <select name="todo_type" class="form-select w-32">
                    <option value="QUESTIONNAIRE">Kuesioner</option>
                    <option value="REPORT">Laporan</option>
                    <option value="TEST">Ujian</option>
                </select>
            </div>
            <div>
                <label class="text-xs text-slate-500 dark:text-slate-300 block mb-0.5">Title</label>
                <input type="text" name="title" placeholder="Judul" required class="form-input w-48">
            </div>
            <div>
                <label class="text-xs text-slate-500 dark:text-slate-300 block mb-0.5">Passing Score</label>
                <input type="number" name="passing_score" placeholder="Score" class="form-input w-24">
            </div>
            <button class="btn-primary">Tambah</button>
        </form>

        <div class="space-y-1">
            @forelse ($course->todos as $todo)
                <div class="flex items-center gap-3 py-2 px-3 rounded {{ $loop->even ? 'bg-slate-50 dark:bg-navy-900/50' : '' }}">
                    <span class="status-badge
                        @if($todo->todo_type === 'QUESTIONNAIRE') status-badge-pending
                        @elseif($todo->todo_type === 'REPORT') status-badge-in-progress
                        @else status-badge-completed @endif">{{ $todo->todo_type }}</span>
                    <span class="text-sm font-medium">{{ $todo->title }}</span>
                    <span class="text-xs text-slate-400 dark:text-slate-500">— Score: {{ $todo->passing_score ?? $course->passing_score }}</span>
                    <form method="POST" action="{{ route('todos.destroy', $todo) }}" class="ml-auto">
                        @csrf @method('DELETE')
                        <button class="text-[#dc2626] hover:underline text-xs">Hapus</button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada todo.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
