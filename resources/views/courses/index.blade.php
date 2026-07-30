@extends('layouts.app')
@section('title', 'Daftar Kursus')
@section('breadcrumbs')
    <span class="text-slate-800 font-medium">In-house Training</span>
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 font-medium">Course List</span>
@endsection
@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-bold text-[#1e3a8a]">Daftar Kursus</h2>
    <a href="{{ route('courses.create') }}" class="btn-primary">+ Tambah</a>
</div>

<x-data-table :headers="['Nama Kursus', 'Kategori', 'Passing Score', 'Retest', '']">
    @forelse ($courses as $course)
    <tr>
        <td class="font-medium">{{ $course->course_name }}</td>
        <td>{{ $course->categoryDetail->detail_name ?? '-' }}</td>
        <td>{{ $course->passing_score }}</td>
        <td class="text-center">{{ $course->has_retest ? 'Ya' : '-' }}</td>
        <td><a href="{{ route('courses.edit', $course) }}" class="text-[#1e3a8a] hover:underline text-xs font-medium">Edit</a></td>
    </tr>
    @empty
    <tr><td colspan="5" class="text-center text-slate-400 py-6">Tidak ada kursus.</td></tr>
    @endforelse
</x-data-table>

<div class="mt-4">{{ $courses->links() }}</div>
@endsection
