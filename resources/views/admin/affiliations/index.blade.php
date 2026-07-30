@extends('layouts.app')
@section('title', 'Affiliation Master')
@section('header-icon')
    <i class="ti ti-building-community"></i>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 dark:text-slate-100 font-medium">Employee Management</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">Affiliation Master</span>
@endsection
@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-bold text-[#1e3a8a]">Affiliation Master</h2>
    <a href="{{ route('admin.affiliations.create') }}" class="btn-primary">+ Add New Affiliation</a>
</div>

<x-data-table :headers="['Affiliation Code', 'Affiliation Name', 'Parent Affiliation', 'Display Order', 'Action']">
    @forelse ($affiliations as $aff)
    <tr>
        <td>{{ $aff->affiliation_code }}</td>
        <td class="font-medium">{{ $aff->affiliation_name }}</td>
        <td>{{ $aff->parent_affiliation_code ?? '-' }}</td>
        <td>{{ $aff->display_order }}</td>
        <td>
            <a href="{{ route('admin.affiliations.edit', $aff) }}" class="text-[#1e3a8a] hover:underline text-xs font-medium">Edit</a>
            <form method="POST" action="{{ route('admin.affiliations.destroy', $aff) }}" class="inline" onsubmit="return confirm('Hapus afiliasi ini?')">
                @csrf @method('DELETE')
                <button class="text-[#dc2626] hover:underline text-xs ml-2">Delete</button>
            </form>
        </td>
    </tr>
    @empty
    <tr><td colspan="5" class="text-center text-slate-400 dark:text-slate-500 py-6">Tidak ada afiliasi.</td></tr>
    @endforelse
</x-data-table>

<div class="mt-4">{{ $affiliations->links() }}</div>
@endsection
