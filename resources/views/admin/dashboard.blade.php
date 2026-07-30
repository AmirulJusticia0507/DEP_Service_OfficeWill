@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<h2 class="text-xl font-bold mb-6">Dashboard</h2>
<div class="grid grid-cols-4 gap-4">
    <div class="bg-white shadow rounded p-4">
        <p class="text-xs text-slate-500">Total Karyawan</p>
        <p class="text-2xl font-bold">{{ $stats['total_employees'] }}</p>
    </div>
    <div class="bg-white shadow rounded p-4">
        <p class="text-xs text-slate-500">Total Kursus</p>
        <p class="text-2xl font-bold">{{ $stats['total_courses'] }}</p>
    </div>
    <div class="bg-white shadow rounded p-4">
        <p class="text-xs text-slate-500">Enrollment Aktif</p>
        <p class="text-2xl font-bold text-sky-600">{{ $stats['active_enrollments'] }}</p>
    </div>
    <div class="bg-white shadow rounded p-4">
        <p class="text-xs text-slate-500">Selesai</p>
        <p class="text-2xl font-bold text-emerald-600">{{ $stats['completed_enrollments'] }}</p>
    </div>
</div>
@endsection
