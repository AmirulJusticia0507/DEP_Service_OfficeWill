@extends('layouts.app')
@section('title', 'Dashboard')
@section('header-icon')
    <i class="ti ti-layout-dashboard"></i>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 font-medium dark:text-slate-100">Dashboard</span>
@endsection
@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white shadow rounded p-5 border-t-2 border-maroon-700 dark:bg-navy-800">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide dark:text-slate-300">Total Karyawan</p>
        <p class="text-2xl font-bold mt-1 dark:text-white">{{ $stats['total_employees'] }}</p>
    </div>
    <div class="bg-white shadow rounded p-5 border-t-2 border-maroon-700 dark:bg-navy-800">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide dark:text-slate-300">Total Kursus</p>
        <p class="text-2xl font-bold mt-1 dark:text-white">{{ $stats['total_courses'] }}</p>
    </div>
    <div class="bg-white shadow rounded p-5 border-t-2 border-gold-500 dark:bg-navy-800">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide dark:text-slate-300">Enrollment Aktif</p>
        <p class="text-2xl font-bold mt-1 text-gold-600 dark:text-gold-400">{{ $stats['active_enrollments'] }}</p>
    </div>
    <div class="bg-white shadow rounded p-5 border-t-2 border-emerald-500 dark:bg-navy-800">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide dark:text-slate-300">Selesai</p>
        <p class="text-2xl font-bold mt-1 text-emerald-600 dark:text-emerald-400">{{ $stats['completed_enrollments'] }}</p>
    </div>
    <div class="bg-white shadow rounded p-5 border-t-2 border-blue-500 dark:bg-navy-800">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide dark:text-slate-300">Sertifikat Terbit</p>
        <p class="text-2xl font-bold mt-1 text-blue-600 dark:text-blue-400">{{ $stats['certificates_issued'] }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white shadow rounded p-5 dark:bg-navy-800">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Completion Rate per Course (Top 10)</h3>
        <canvas id="completionChart" height="200"></canvas>
    </div>
    <div class="bg-white shadow rounded p-5 dark:bg-navy-800">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Monthly Completions</h3>
        <canvas id="monthlyChart" height="200"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Chart(document.getElementById('completionChart'), {
        type: 'bar',
        data: {
            labels: {!! $chartLabels !!},
            datasets: [
                { label: 'Enrolled', data: {!! $chartEnrolled !!}, backgroundColor: '#D4A01760', borderColor: '#D4A017', borderWidth: 1 },
                { label: 'Completed', data: {!! $chartCompleted !!}, backgroundColor: '#10b98160', borderColor: '#10b981', borderWidth: 1 }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { labels: { font: { size: 11 } } } },
            scales: {
                x: { ticks: { font: { size: 10 } } },
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: {!! $monthlyLabels !!},
            datasets: [{
                label: 'Completed',
                data: {!! $monthlyCounts !!},
                borderColor: '#380812',
                backgroundColor: '#38081220',
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#D4A017',
                pointBorderColor: '#D4A017',
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { labels: { font: { size: 11 } } } },
            scales: {
                x: { ticks: { font: { size: 10 } } },
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
});
</script>
@endsection
