@extends('layouts.app')
@section('title', 'Post-Learning ToDo Answer Inquiry')
@section('header-icon')
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 font-medium">Administration</span>
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 font-medium">ToDo Answer Inquiry</span>
@endsection
@section('content')
<h2 class="text-lg font-bold text-[#1e3a8a] mb-4">Post-Learning ToDo Answer Inquiry</h2>

<div class="bg-white rounded shadow mb-4">
    <div class="p-3 border-b border-slate-200">
        <form class="flex flex-wrap gap-2 items-end">
            <div>
                <label class="text-xs text-slate-500 block mb-0.5">Pilih Kursus</label>
                <select name="course_id" class="form-select w-64" onchange="this.form.submit()">
                    <option value="">— Pilih kursus —</option>
                    @foreach ($courses as $c)
                        <option value="{{ $c->id }}" @selected(request('course_id') == $c->id)>{{ $c->course_name }}</option>
                    @endforeach
                </select>
            </div>
            @if($todos->isNotEmpty())
            <div>
                <label class="text-xs text-slate-500 block mb-0.5">Pilih ToDo</label>
                <select name="todo_id" class="form-select w-64" onchange="this.form.submit()">
                    <option value="">— Pilih todo —</option>
                    @foreach ($todos as $t)
                        <option value="{{ $t->id }}" @selected(request('todo_id') == $t->id)>{{ $t->title }} ({{ $t->todo_type }})</option>
                    @endforeach
                </select>
            </div>
            @endif
            <noscript><button class="btn-primary">Lihat</button></noscript>
        </form>
    </div>
</div>

@if($selectedTodo)
    <h3 class="font-semibold text-sm mb-3 text-[#1e3a8a]">{{ $selectedTodo->course->course_name }} — {{ $selectedTodo->title }}</h3>

    <x-data-table :headers="['Karyawan', 'Response', 'Score', 'Status']">
        @forelse ($responses as $r)
        <tr>
            <td>{{ $r->enrollment->employee->full_name ?? '-' }}</td>
            <td class="max-w-xs truncate">{{ Str::limit($r->response_content ?? '-', 80) }}</td>
            <td>{{ $r->score ?? '-' }}</td>
            <td>
                @if($r->status === 'PASSED')
                    <x-status-badge status="COMPLETED">PASSED</x-status-badge>
                @elseif($r->status === 'FAILED')
                    <x-status-badge status="CANCELLED">FAILED</x-status-badge>
                @else
                    <x-status-badge status="ENROLLED">{{ $r->status ?? 'PENDING' }}</x-status-badge>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center text-slate-400 py-6">Belum ada jawaban.</td></tr>
        @endforelse
    </x-data-table>
@endif
@endsection
