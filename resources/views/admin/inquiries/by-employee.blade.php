@extends('layouts.app')
@section('title', 'Inkuiri per Karyawan')
@section('content')
<h2 class="text-xl font-bold mb-4">Inkuiri per Karyawan</h2>
<form class="mb-4 flex gap-2">
    <select name="employee_id" class="border rounded px-3 py-2 text-sm">
        <option value="">Pilih karyawan</option>
        @foreach ($employees as $emp)
            <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
        @endforeach
    </select>
    <button class="bg-slate-200 rounded px-3 py-2 text-sm">Lihat</button>
</form>

@if($selectedEmployee)
    <h3 class="font-semibold mb-2">{{ $selectedEmployee->full_name }}</h3>
    <table class="w-full text-sm bg-white shadow rounded">
        <thead class="bg-slate-100">
            <tr><th class="p-2">Kursus</th><th class="p-2">Deadline</th><th class="p-2">Status</th><th class="p-2">Progress</th></tr>
        </thead>
        <tbody>
            @foreach ($enrollments as $enr)
            <tr class="border-t">
                <td class="p-2">{{ $enr->course->course_name }}</td>
                <td class="p-2">{{ $enr->enrollment_deadline }}</td>
                <td class="p-2">
                    <span class="px-2 py-0.5 rounded text-xs font-medium
                        @if($enr->status === 'ENROLLED') bg-sky-100 text-sky-800
                        @elseif($enr->status === 'COMPLETED') bg-emerald-100 text-emerald-800
                        @else bg-rose-100 text-rose-800 @endif">{{ $enr->status }}</span>
                </td>
                <td class="p-2">{{ $enr->todoResponses->count() }} todo</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">{{ $enrollments->links() }}</div>
@endif
@endsection
