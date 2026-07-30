@extends('layouts.app')
@section('title', 'Daftar Kursus')
@section('header-icon')
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
@endsection
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

<x-data-table :headers="['', 'Nama Kursus', 'Kategori', 'Passing Score', 'Retest', '']">
    @forelse ($courses as $course)
    <tr>
        <td class="w-14">
            @if ($course->photo)
                <img src="{{ Storage::url($course->photo) }}" alt="" class="w-12 h-9 object-cover rounded border border-slate-200">
            @else
                <div class="w-12 h-9 bg-slate-100 rounded border border-slate-200 flex items-center justify-center text-slate-300 text-xs">No</div>
            @endif
        </td>
        <td class="font-medium">{{ $course->course_name }}</td>
        <td>{{ $course->categoryDetail->detail_name ?? '-' }}</td>
        <td>{{ $course->passing_score }}</td>
        <td class="text-center">{{ $course->has_retest ? 'Ya' : '-' }}</td>
        <td><a href="{{ route('courses.edit', $course) }}" class="text-[#1e3a8a] hover:underline text-xs font-medium">Edit</a></td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center text-slate-400 py-6">Tidak ada kursus.</td></tr>
    @endforelse
</x-data-table>

<div class="mt-4">{{ $courses->links() }}</div>
@endsection
