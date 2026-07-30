@extends('layouts.app')
@section('title', isset($affiliation) ? 'Edit Affiliation' : 'New Affiliation')
@section('breadcrumbs')
    <span class="text-slate-800 font-medium">Employee Management</span>
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 font-medium">Affiliation Master</span>
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 font-medium">{{ isset($affiliation) ? 'Edit' : 'Add New' }}</span>
@endsection
@section('content')
<h2 class="text-lg font-bold text-[#1e3a8a] mb-4">{{ isset($affiliation) ? 'Edit Affiliation' : 'Add New Affiliation' }}</h2>

<form method="POST" class="bg-white shadow rounded max-w-2xl">
    @csrf
    @if(isset($affiliation)) @method('PUT') @endif

    <x-form-section-header>Affiliation Information</x-form-section-header>
    <div class="p-4 space-y-3">
        <div>
            <label class="block text-sm mb-1"><x-required-mark /> Affiliation Code</label>
            <input type="text" name="affiliation_code" value="{{ old('affiliation_code', $affiliation->affiliation_code ?? '') }}" required class="form-input">
        </div>
        <div>
            <label class="block text-sm mb-1"><x-required-mark /> Affiliation Name</label>
            <input type="text" name="affiliation_name" value="{{ old('affiliation_name', $affiliation->affiliation_name ?? '') }}" required class="form-input">
        </div>
        <div>
            <label class="block text-sm mb-1">Parent Affiliation</label>
            <select name="parent_affiliation_code" class="form-select">
                <option value="">— No parent —</option>
                @foreach ($parentAffiliations as $pa)
                    <option value="{{ $pa->affiliation_code }}" @selected(old('parent_affiliation_code', $affiliation->parent_affiliation_code ?? '') === $pa->affiliation_code)>{{ $pa->affiliation_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">Display Order</label>
            <input type="number" name="display_order" value="{{ old('display_order', $affiliation->display_order ?? 0) }}" min="0" class="form-input w-24">
        </div>
        <div>
            <label class="block text-sm mb-1">Organization Type</label>
            <select name="organization_type" class="form-select w-48">
                <option value="">— Select —</option>
                <option value="1" @selected(old('organization_type', $affiliation->organization_type ?? '') == 1)>Main Store</option>
                <option value="2" @selected(old('organization_type', $affiliation->organization_type ?? '') == 2)>FC Store</option>
            </select>
        </div>
    </div>

    <x-action-buttons align="left" class="p-4 border-t border-slate-200">
        <button type="submit" class="btn-primary">{{ isset($affiliation) ? 'Update' : 'Save' }}</button>
        <a href="{{ route('admin.affiliations.index') }}" class="btn-secondary">Cancel</a>
    </x-action-buttons>
</form>
@endsection
