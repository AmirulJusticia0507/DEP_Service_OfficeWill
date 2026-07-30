@extends('layouts.app')
@section('title', $course->course_name)
@section('content')
<h2 class="text-xl font-bold mb-4">{{ $course->course_name }}</h2>

<h3 class="font-semibold mb-2">Materi</h3>
@foreach ($course->materials as $mat)
    <div class="bg-white shadow rounded p-3 mb-2 flex items-center gap-3">
        <span class="px-2 py-0.5 rounded text-xs font-medium {{ $mat->material_type === 'YOUTUBE' ? 'bg-rose-100 text-rose-700' : 'bg-sky-100 text-sky-700' }}">{{ $mat->material_type }}</span>
        <span class="text-sm">{{ $mat->title }}</span>
        @if($mat->material_type === 'YOUTUBE')
            <a href="{{ $mat->content_url_or_path }}" target="_blank" class="text-indigo-600 hover:underline text-xs ml-auto">Tonton</a>
        @else
            <a href="{{ $mat->content_url_or_path }}" target="_blank" class="text-indigo-600 hover:underline text-xs ml-auto">Buka PDF</a>
        @endif
    </div>
@endforeach

<h3 class="font-semibold mt-6 mb-2">Todo</h3>
@foreach ($course->todos as $todo)
    <div class="bg-white shadow rounded p-4 mb-3">
        <div class="flex items-center justify-between mb-2">
            <span class="px-2 py-0.5 rounded text-xs font-medium
                @if($todo->todo_type === 'QUESTIONNAIRE') bg-purple-100 text-purple-700
                @elseif($todo->todo_type === 'REPORT') bg-amber-100 text-amber-700
                @else bg-teal-100 text-teal-700 @endif">{{ $todo->todo_type }}</span>
            <strong class="text-sm">{{ $todo->title }}</strong>
        </div>
        @if($todo->description)
            <p class="text-xs text-slate-500 mb-2">{{ $todo->description }}</p>
        @endif

        @php
            $response = $enrollment->todoResponses->where('course_todo_id', $todo->id)->first();
        @endphp

        @if($response && $response->status === 'PASSED')
            <span class="text-emerald-600 text-xs font-medium">✓ Selesai</span>
        @elseif($response && $response->status === 'FAILED')
            <span class="text-rose-600 text-xs font-medium">✗ Tidak lulus ({{ $response->score }})</span>
        @endif

        @if($todo->todo_type === 'QUESTIONNAIRE')
            <form method="POST" action="{{ route('todos.questionnaire', $todo) }}" class="mt-2">
                @csrf
                <textarea name="response_content" rows="2" placeholder="Jawaban Anda..." class="w-full border rounded px-2 py-1.5 text-xs"></textarea>
                <button type="submit" class="bg-purple-600 text-white rounded px-3 py-1 text-xs mt-1">Kirim</button>
            </form>
        @elseif($todo->todo_type === 'REPORT')
            <form method="POST" action="{{ route('todos.report', $todo) }}" enctype="multipart/form-data" class="mt-2">
                @csrf
                <input type="file" name="report_file" class="text-xs">
                <button type="submit" class="bg-amber-600 text-white rounded px-3 py-1 text-xs mt-1">Upload</button>
            </form>
        @elseif($todo->todo_type === 'TEST')
            <form method="POST" action="{{ route('todos.test', $todo) }}" class="mt-2 flex gap-2">
                @csrf
                <input type="number" name="score" placeholder="Nilai" max="100" class="w-20 border rounded px-2 py-1 text-xs">
                <button type="submit" class="bg-teal-600 text-white rounded px-3 py-1 text-xs">Submit</button>
            </form>
        @endif
    </div>
@endforeach

@if($enrollment->status === 'ENROLLED')
    <form method="POST" action="{{ route('attendance.complete', $enrollment) }}" class="mt-4">
        @csrf
        <button type="submit" class="bg-emerald-600 text-white rounded px-6 py-2 text-sm font-medium">Selesaikan Kursus</button>
    </form>
@endif
@endsection
