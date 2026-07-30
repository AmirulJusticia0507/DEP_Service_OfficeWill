@extends('layouts.app')
@section('title', 'Penugasan')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-xl font-bold">Riwayat Penugasan</h2>
    <a href="{{ route('admin.assignments.create') }}" class="bg-indigo-600 text-white rounded px-4 py-2 text-sm">Tugaskan Kursus</a>
</div>
<table class="w-full text-sm bg-white shadow rounded">
    <thead class="bg-slate-100">
        <tr><th class="p-2">Kursus</th><th class="p-2">Karyawan</th><th class="p-2">Deadline</th><th class="p-2">Status</th><th class="p-2"></th></tr>
    </thead>
    <tbody>
        @foreach ($assignments as $a)
        <tr class="border-t">
            <td class="p-2">{{ $a->course->course_name }}</td>
            <td class="p-2">{{ $a->employee->full_name }}</td>
            <td class="p-2">{{ $a->enrollment_deadline }}</td>
            <td class="p-2">
                <span class="px-2 py-0.5 rounded text-xs font-medium
                    @if($a->status === 'ENROLLED') bg-sky-100 text-sky-800
                    @elseif($a->status === 'COMPLETED') bg-emerald-100 text-emerald-800
                    @else bg-rose-100 text-rose-800 @endif">{{ $a->status }}</span>
            </td>
            <td class="p-2">
                @if($a->status === 'ENROLLED')
                <form method="POST" action="{{ route('admin.assignments.cancel', $a) }}" class="inline">
                    @csrf
                    <button class="text-rose-600 hover:underline text-xs">Batalkan</button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="mt-4">{{ $assignments->links() }}</div>
@endsection
