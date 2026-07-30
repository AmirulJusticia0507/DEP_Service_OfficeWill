@extends('layouts.app')
@section('title', 'Dashboard')
@section('breadcrumbs')
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 font-medium">Dashboard</span>
@endsection
@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white shadow rounded p-5 border-t-2 border-[#1e3a8a]">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Total Karyawan</p>
        <p class="text-2xl font-bold mt-1">{{ $stats['total_employees'] }}</p>
    </div>
    <div class="bg-white shadow rounded p-5 border-t-2 border-[#1e3a8a]">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Total Kursus</p>
        <p class="text-2xl font-bold mt-1">{{ $stats['total_courses'] }}</p>
    </div>
    <div class="bg-white shadow rounded p-5 border-t-2 border-[#0284c7]">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Enrollment Aktif</p>
        <p class="text-2xl font-bold mt-1 text-[#0284c7]">{{ $stats['active_enrollments'] }}</p>
    </div>
    <div class="bg-white shadow rounded p-5 border-t-2 border-emerald-500">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Selesai</p>
        <p class="text-2xl font-bold mt-1 text-emerald-600">{{ $stats['completed_enrollments'] }}</p>
    </div>
</div>
@endsection
