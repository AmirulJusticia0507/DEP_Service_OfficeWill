@extends('layouts.app')
@section('title', 'Kursus')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-xl font-bold">Daftar Kursus</h2>
    <a href="{{ route('courses.create') }}" class="bg-indigo-600 text-white rounded px-4 py-2 text-sm">Tambah</a>
</div>
<table class="w-full text-sm bg-white shadow rounded">
    <thead class="bg-slate-100">
        <tr><th class="p-2 text-left">Nama</th><th class="p-2 text-left">Kategori</th><th class="p-2 text-left">Passing Score</th><th class="p-2">Retest</th><th class="p-2"></th></tr>
    </thead>
    <tbody>
        @foreach ($courses as $course)
        <tr class="border-t">
            <td class="p-2">{{ $course->course_name }}</td>
            <td class="p-2">{{ $course->categoryDetail->detail_name ?? '-' }}</td>
            <td class="p-2">{{ $course->passing_score }}</td>
            <td class="p-2 text-center">{{ $course->has_retest ? 'Ya' : '-' }}</td>
            <td class="p-2"><a href="{{ route('courses.edit', $course) }}" class="text-indigo-600 hover:underline">Edit</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="mt-4">{{ $courses->links() }}</div>
@endsection
