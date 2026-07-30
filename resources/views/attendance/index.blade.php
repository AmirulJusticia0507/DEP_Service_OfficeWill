@extends('layouts.app')
@section('title', 'Absensi Saya')
@section('content')
<h2 class="text-xl font-bold mb-4">Daftar Kursus Saya</h2>
@forelse ($enrollments as $enr)
    <div class="bg-white shadow rounded p-4 mb-3 flex items-center justify-between">
        <div>
            <h3 class="font-semibold">{{ $enr->course->course_name }}</h3>
            <p class="text-xs text-slate-500">Deadline: {{ $enr->enrollment_deadline }}</p>
        </div>
        <a href="{{ route('attendance.show', $enr->course_id) }}" class="bg-indigo-600 text-white rounded px-3 py-1.5 text-xs">Mulai</a>
    </div>
@empty
    <p class="text-slate-400">Tidak ada kursus aktif.</p>
@endforelse
<div class="mt-4">{{ $enrollments->links() }}</div>
@endsection
