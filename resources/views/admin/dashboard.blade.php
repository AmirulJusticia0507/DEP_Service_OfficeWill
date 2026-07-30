@extends('layouts.app')
@section('title', 'Dashboard')
@section('header-icon')
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 font-medium dark:text-slate-100">Dashboard</span>
@endsection
@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white shadow rounded p-5 border-t-2 border-[#1e3a8a] dark:bg-navy-800 dark:border-blue-soft">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide dark:text-slate-300">Total Karyawan</p>
        <p class="text-2xl font-bold mt-1 dark:text-white">{{ $stats['total_employees'] }}</p>
    </div>
    <div class="bg-white shadow rounded p-5 border-t-2 border-[#1e3a8a] dark:bg-navy-800 dark:border-blue-soft">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide dark:text-slate-300">Total Kursus</p>
        <p class="text-2xl font-bold mt-1 dark:text-white">{{ $stats['total_courses'] }}</p>
    </div>
    <div class="bg-white shadow rounded p-5 border-t-2 border-[#0284c7] dark:bg-navy-800">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide dark:text-slate-300">Enrollment Aktif</p>
        <p class="text-2xl font-bold mt-1 text-[#0284c7] dark:text-blue-accent">{{ $stats['active_enrollments'] }}</p>
    </div>
    <div class="bg-white shadow rounded p-5 border-t-2 border-emerald-500 dark:bg-navy-800">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide dark:text-slate-300">Selesai</p>
        <p class="text-2xl font-bold mt-1 text-emerald-600 dark:text-emerald-400">{{ $stats['completed_enrollments'] }}</p>
    </div>
</div>
@endsection
