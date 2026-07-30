@extends('layouts.app')
@section('title', 'Edit Kursus')
@section('content')
<h2 class="text-xl font-bold mb-4">Edit Kursus: {{ $course->course_name }}</h2>

<form method="POST" class="max-w-xl bg-white shadow rounded p-6 space-y-3 mb-8">
    @csrf
    @method('PUT')
    <div>
        <label class="block text-sm mb-1">Kategori Detail</label>
        <select name="category_detail_id" class="w-full border rounded px-3 py-2 text-sm">
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
        <label class="block text-sm mb-1">Nama Kursus</label>
        <input type="text" name="course_name" value="{{ $course->course_name }}" required class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm mb-1">Deskripsi</label>
        <textarea name="description" rows="3" class="w-full border rounded px-3 py-2 text-sm">{{ $course->description }}</textarea>
    </div>
    <div class="flex gap-4">
        <div>
            <label class="block text-sm mb-1">Passing Score</label>
            <input type="number" name="passing_score" value="{{ $course->passing_score }}" class="w-24 border rounded px-2 py-2 text-sm">
        </div>
        <label class="flex items-center gap-2 text-sm mt-6"><input type="checkbox" name="has_retest" value="1" @checked($course->has_retest)> Ada Retest</label>
    </div>
    <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-2 text-sm">Simpan</button>
</form>

<h3 class="text-lg font-bold mb-2">Materi</h3>
<form method="POST" action="{{ route('courses.materials.store', $course) }}" class="flex gap-2 mb-4">
    @csrf
    <select name="material_type" class="border rounded px-2 text-sm">
        <option value="YOUTUBE">YouTube</option>
        <option value="PDF">PDF</option>
    </select>
    <input type="text" name="title" placeholder="Judul" required class="border rounded px-2 text-sm flex-1">
    <input type="text" name="content_url_or_path" placeholder="URL / Path" required class="border rounded px-2 text-sm flex-1">
    <input type="number" name="display_order" placeholder="Urutan" class="border rounded px-2 text-sm w-16">
    <button class="bg-sky-600 text-white rounded px-3 text-sm">Tambah</button>
</form>
@foreach ($course->materials as $mat)
    <div class="text-sm flex items-center gap-2 mb-1">
        <span class="px-1.5 py-0.5 rounded text-xs {{ $mat->material_type === 'YOUTUBE' ? 'bg-rose-100 text-rose-700' : 'bg-sky-100 text-sky-700' }}">{{ $mat->material_type }}</span>
        <span>{{ $mat->title }}</span>
        <span class="text-slate-400">— {{ $mat->content_url_or_path }}</span>
        <form method="POST" action="{{ route('materials.destroy', $mat) }}" class="inline">
            @csrf @method('DELETE')
            <button class="text-rose-500 hover:underline text-xs">Hapus</button>
        </form>
    </div>
@endforeach

<h3 class="text-lg font-bold mt-6 mb-2">Todo</h3>
<form method="POST" action="{{ route('courses.todos.store', $course) }}" class="flex gap-2 mb-4">
    @csrf
    <select name="todo_type" class="border rounded px-2 text-sm">
        <option value="QUESTIONNAIRE">Kuesioner</option>
        <option value="REPORT">Laporan</option>
        <option value="TEST">Ujian</option>
    </select>
    <input type="text" name="title" placeholder="Judul" required class="border rounded px-2 text-sm flex-1">
    <input type="number" name="passing_score" placeholder="Passing Score" class="border rounded px-2 text-sm w-28">
    <button class="bg-sky-600 text-white rounded px-3 text-sm">Tambah</button>
</form>
@foreach ($course->todos as $todo)
    <div class="text-sm flex items-center gap-2 mb-1">
        <span class="px-1.5 py-0.5 rounded text-xs
            @if($todo->todo_type === 'QUESTIONNAIRE') bg-purple-100 text-purple-700
            @elseif($todo->todo_type === 'REPORT') bg-amber-100 text-amber-700
            @else bg-teal-100 text-teal-700 @endif">{{ $todo->todo_type }}</span>
        <span>{{ $todo->title }}</span>
        <span class="text-slate-400">— Score: {{ $todo->passing_score ?? $course->passing_score }}</span>
        <form method="POST" action="{{ route('todos.destroy', $todo) }}" class="inline">
            @csrf @method('DELETE')
            <button class="text-rose-500 hover:underline text-xs">Hapus</button>
        </form>
    </div>
@endforeach
@endsection
