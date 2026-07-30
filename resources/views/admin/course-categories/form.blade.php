@extends('layouts.app')
@section('title', isset($category) ? 'Edit Classification' : 'New Classification')
@section('breadcrumbs')
    <span class="text-slate-800 font-medium">Course Registration</span>
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 font-medium">Course Classification</span>
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 font-medium">{{ isset($category) ? 'Edit' : 'Add New' }}</span>
@endsection
@section('content')
<h2 class="text-lg font-bold text-[#1e3a8a] mb-4">{{ isset($category) ? 'Edit Classification' : 'Add New Classification' }}</h2>

<form method="POST" class="bg-white shadow rounded max-w-2xl">
    @csrf
    @if(isset($category)) @method('PUT') @endif

    <x-form-section-header>Classification Information</x-form-section-header>
    <div class="p-4 space-y-3">
        <div>
            <label class="block text-sm mb-1"><x-required-mark /> Category Code</label>
            <input type="text" name="category_code" value="{{ old('category_code', $category->category_code ?? '') }}" required class="form-input">
        </div>
        <div>
            <label class="block text-sm mb-1"><x-required-mark /> Category Name</label>
            <input type="text" name="category_name" value="{{ old('category_name', $category->category_name ?? '') }}" required class="form-input">
        </div>
        <div>
            <label class="block text-sm mb-1">Display Order</label>
            <input type="number" name="display_order" value="{{ old('display_order', $category->display_order ?? 0) }}" min="0" class="form-input w-24">
        </div>
    </div>

    <x-action-buttons align="left" class="p-4 border-t border-slate-200">
        <button type="submit" class="btn-primary">{{ isset($category) ? 'Update' : 'Save' }}</button>
        <a href="{{ route('admin.course-categories.index') }}" class="btn-secondary">Cancel</a>
    </x-action-buttons>
</form>
@endsection
