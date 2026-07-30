@extends('layouts.app')
@section('title', 'Enrollment')
@section('content')
<h2 class="text-xl font-bold mb-4">Enrollment Kursus</h2>
<form class="mb-4 flex gap-2">
    <input type="text" name="course_id" value="{{ request('course_id') }}" placeholder="Course ID" class="border rounded px-2 py-1.5 text-sm">
    <input type="text" name="employee_id" value="{{ request('employee_id') }}" placeholder="Employee ID" class="border rounded px-2 py-1.5 text-sm">
    <select name="status" class="border rounded px-2 py-1.5 text-sm">
        <option value="">Semua status</option>
        <option value="ENROLLED" @selected(request('status') === 'ENROLLED')>ENROLLED</option>
        <option value="COMPLETED" @selected(request('status') === 'COMPLETED')>COMPLETED</option>
        <option value="CANCELLED" @selected(request('status') === 'CANCELLED')>CANCELLED</option>
    </select>
    <button class="bg-slate-200 rounded px-3 py-1.5 text-sm">Filter</button>
</form>
<table class="w-full text-sm bg-white shadow rounded">
    <thead class="bg-slate-100">
        <tr><th class="p-2">Kursus</th><th class="p-2">Karyawan</th><th class="p-2">Deadline</th><th class="p-2">Status</th><th class="p-2"></th></tr>
    </thead>
    <tbody>
        @foreach ($enrollments as $enr)
        <tr class="border-t">
            <td class="p-2">{{ $enr->course->course_name }}</td>
            <td class="p-2">{{ $enr->employee->full_name }}</td>
            <td class="p-2">{{ $enr->enrollment_deadline }}</td>
            <td class="p-2">
                <span class="px-2 py-0.5 rounded text-xs font-medium
                    @if($enr->status === 'ENROLLED') bg-sky-100 text-sky-800
                    @elseif($enr->status === 'COMPLETED') bg-emerald-100 text-emerald-800
                    @else bg-rose-100 text-rose-800 @endif">{{ $enr->status }}</span>
            </td>
            <td class="p-2">
                @if($enr->status === 'ENROLLED')
                <form method="POST" action="{{ route('enrollments.update', $enr) }}" class="inline">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="CANCELLED">
                    <button class="text-rose-600 hover:underline text-xs">Batalkan</button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="mt-4">{{ $enrollments->links() }}</div>
@endsection
