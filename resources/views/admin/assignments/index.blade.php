@extends('layouts.app')
@section('title', 'Penugasan')
@section('header-icon')
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 font-medium">Administration</span>
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 font-medium">Assignment History</span>
@endsection
@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-bold text-[#1e3a8a]">Riwayat Penugasan</h2>
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
                <button class="text-[#dc2626] hover:underline text-xs">Batalkan</button>
            </form>
            @endif
        </td>
    </tr>
    @empty
    <tr><td colspan="5" class="text-center text-slate-400 py-6">Tidak ada penugasan.</td></tr>
    @endforelse
</x-data-table>

<div class="mt-4">{{ $assignments->links() }}</div>
@endsection
