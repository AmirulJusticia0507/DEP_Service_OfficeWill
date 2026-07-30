@extends('layouts.app')
@section('title', isset($position) ? 'Edit Position' : 'New Position')
@section('header-icon')
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 dark:text-slate-100 font-medium">Employee Management</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">Position Master</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">{{ isset($position) ? 'Edit' : 'Add New' }}</span>
@endsection
@section('content')
<h2 class="text-lg font-bold text-[#1e3a8a] mb-4">{{ isset($position) ? 'Edit Position' : 'Add New Position' }}</h2>

<form method="POST" class="bg-white dark:bg-navy-800 shadow rounded max-w-2xl">
    @csrf
    @if(isset($position)) @method('PUT') @endif

    <x-form-section-header>Position Information</x-form-section-header>
    <div class="p-4 space-y-3">
        <div>
            <label class="block text-sm mb-1"><x-required-mark /> Job ID</label>
            <input type="text" name="job_id" value="{{ old('job_id', $position->job_id ?? '') }}" required class="form-input">
        </div>
        <div>
            <label class="block text-sm mb-1"><x-required-mark /> Job Title</label>
            <input type="text" name="job_title" value="{{ old('job_title', $position->job_title ?? '') }}" required class="form-input">
        </div>
        <div>
            <label class="block text-sm mb-1">Display Order</label>
            <input type="number" name="display_order" value="{{ old('display_order', $position->display_order ?? 0) }}" min="0" class="form-input w-24">
        </div>
    </div>

    <x-action-buttons align="left" class="p-4 border-t border-slate-200 dark:border-t dark:border-slate-700">
        <button type="submit" class="btn-primary">{{ isset($position) ? 'Update' : 'Save' }}</button>
        <a href="{{ route('admin.positions.index') }}" class="btn-secondary">Cancel</a>
    </x-action-buttons>
</form>
@endsection
