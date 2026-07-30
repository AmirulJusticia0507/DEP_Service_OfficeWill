@extends('layouts.app')
@section('title', 'Penugasan')
@section('header-icon')
    <i class="ti ti-clipboard-check"></i>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 dark:text-slate-100 font-medium">Administration</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">Assignment History</span>
@endsection
@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-bold text-primary">Riwayat Penugasan</h2>
    <a href="{{ route('admin.assignments.create') }}" class="btn-primary">+ Tugaskan Kursus</a>
</div>

<x-data-table :headers="['Kursus', 'Karyawan', 'Deadline', 'Status', '']">
    @forelse ($assignments as $a)
    <tr>
        <td>{{ $a->course->course_name }}</td>
        <td>{{ $a->employee->full_name }}</td>
        <td>{{ $a->enrollment_deadline }}</td>
        <td>
            @if($a->status === 'ENROLLED')
                <x-status-badge status="ENROLLED">ENROLLED</x-status-badge>
            @elseif($a->status === 'COMPLETED')
                <x-status-badge status="COMPLETED">COMPLETED</x-status-badge>
            @else
                <x-status-badge status="CANCELLED">CANCELLED</x-status-badge>
            @endif
        </td>
        <td>
            @if($a->status === 'ENROLLED')
            <form method="POST" action="{{ route('admin.assignments.cancel', $a) }}" class="inline">
                @csrf
                <button class="text-rose-500 hover:underline text-xs">Batalkan</button>
            </form>
            @endif
        </td>
    </tr>
    @empty
    <tr><td colspan="5" class="text-center text-slate-400 dark:text-slate-500 py-6">Tidak ada penugasan.</td></tr>
    @endforelse
</x-data-table>

<div class="mt-4">{{ $assignments->links() }}</div>
@endsection
