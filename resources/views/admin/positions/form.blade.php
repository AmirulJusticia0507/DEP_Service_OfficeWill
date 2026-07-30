@extends('layouts.app')
@section('title', isset($position) ? 'Edit Position' : 'New Position')
@section('breadcrumbs')
    <span class="text-slate-800 font-medium">Employee Management</span>
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 font-medium">Position Master</span>
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 font-medium">{{ isset($position) ? 'Edit' : 'Add New' }}</span>
@endsection
@section('content')
<h2 class="text-lg font-bold text-[#1e3a8a] mb-4">{{ isset($position) ? 'Edit Position' : 'Add New Position' }}</h2>

<form method="POST" class="bg-white shadow rounded max-w-2xl">
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

    <x-action-buttons align="left" class="p-4 border-t border-slate-200">
        <button type="submit" class="btn-primary">{{ isset($position) ? 'Update' : 'Save' }}</button>
        <a href="{{ route('admin.positions.index') }}" class="btn-secondary">Cancel</a>
    </x-action-buttons>
</form>
@endsection
