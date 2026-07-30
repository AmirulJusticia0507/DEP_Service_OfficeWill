@extends('layouts.app')
@section('title', isset($category) ? 'Edit Classification' : 'New Classification')
@section('header-icon')
    <i class="ti ti-folder"></i>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 dark:text-slate-100 font-medium">Course Registration</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">Course Classification</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">{{ isset($category) ? 'Edit' : 'Add New' }}</span>
@endsection
@section('content')
<h2 class="text-lg font-bold text-[#1e3a8a] mb-4">{{ isset($category) ? 'Edit Classification' : 'Add New Classification' }}</h2>

<form method="POST" enctype="multipart/form-data" class="bg-white dark:bg-navy-800 shadow rounded max-w-2xl">
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
        <div>
            <label class="block text-sm mb-1">Icon</label>
            <input type="file" name="icon" accept="image/png,image/jpg,image/jpeg,image/svg+xml" class="form-input">
            @if(isset($category) && $category->icon)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $category->icon) }}" class="w-10 h-10 object-contain rounded border">
                </div>
            @endif
        </div>
    </div>

    <x-action-buttons align="left" class="p-4 border-t border-slate-200 dark:border-t dark:border-slate-700">
        <button type="submit" class="btn-primary">{{ isset($category) ? 'Update' : 'Save' }}</button>
        <a href="{{ route('admin.course-categories.index') }}" class="btn-secondary">Cancel</a>
    </x-action-buttons>
</form>
@endsection
