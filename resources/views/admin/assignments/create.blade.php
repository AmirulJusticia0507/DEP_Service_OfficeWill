@extends('layouts.app')
@section('title', 'Tugaskan Kursus')
@section('content')
<h2 class="text-xl font-bold mb-4">Tugaskan Kursus ke Karyawan</h2>
<form method="POST" class="max-w-2xl bg-white shadow rounded p-6 space-y-3">
    @csrf
    <div>
        <label class="block text-sm mb-1">Kursus</label>
        <select name="course_id" class="w-full border rounded px-3 py-2 text-sm">
            @foreach ($courses as $c)
                <option value="{{ $c->id }}">{{ $c->course_name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm mb-1">Karyawan</label>
        <div class="max-h-48 overflow-y-auto border rounded p-2 space-y-1">
            @foreach ($employees as $emp)
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}">
                    {{ $emp->full_name }} ({{ $emp->employee_code }})
                </label>
            @endforeach
        </div>
    </div>
    <div>
        <label class="block text-sm mb-1">Batas Waktu</label>
        <input type="date" name="enrollment_deadline" required class="border rounded px-3 py-2 text-sm">
    </div>
    <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-2 text-sm">Tugaskan</button>
</form>
@endsection
