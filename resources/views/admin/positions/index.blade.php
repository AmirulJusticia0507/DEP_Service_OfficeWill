@extends('layouts.app')
@section('title', 'Position Master')
@section('breadcrumbs')
    <span class="text-slate-800 font-medium">Employee Management</span>
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 font-medium">Position Master</span>
@endsection
@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-bold text-[#1e3a8a]">Position Master</h2>
    <a href="{{ route('admin.positions.create') }}" class="btn-primary">+ Add New Position</a>
</div>

<x-data-table :headers="['Job ID', 'Job Title', 'Display Order', 'Action']">
    @forelse ($positions as $pos)
    <tr>
        <td>{{ $pos->job_id }}</td>
        <td class="font-medium">{{ $pos->job_title }}</td>
        <td>{{ $pos->display_order }}</td>
        <td>
            <a href="{{ route('admin.positions.edit', $pos) }}" class="text-[#1e3a8a] hover:underline text-xs font-medium">Edit</a>
            <form method="POST" action="{{ route('admin.positions.destroy', $pos) }}" class="inline" onsubmit="return confirm('Hapus jabatan ini?')">
                @csrf @method('DELETE')
                <button class="text-[#dc2626] hover:underline text-xs ml-2">Delete</button>
            </form>
        </td>
    </tr>
    @empty
    <tr><td colspan="4" class="text-center text-slate-400 py-6">Tidak ada jabatan.</td></tr>
    @endforelse
</x-data-table>

<div class="mt-4">{{ $positions->links() }}</div>
@endsection
