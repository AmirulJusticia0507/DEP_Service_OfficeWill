@extends('layouts.app')
@section('title', 'Tambah Kursus')
@section('header-icon')
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 font-medium">In-house Training</span>
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 font-medium">Course Registration</span>
@endsection
@section('content')
<h2 class="text-lg font-bold text-[#1e3a8a] mb-4">Tambah Kursus</h2>

<form method="POST" enctype="multipart/form-data" class="bg-white shadow rounded max-w-2xl">
    @csrf

    <x-form-section-header>Course Information</x-form-section-header>
    <div class="p-4 space-y-3">
        <div>
            <label class="block text-sm mb-1"><x-required-mark /> Kategori Detail</label>
            <select name="category_detail_id" class="form-select">
                @foreach ($categories as $cat)
                    <optgroup label="{{ $cat->category_name }}">
                        @foreach ($cat->details as $det)
                            <option value="{{ $det->id }}">{{ $det->detail_name }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1"><x-required-mark /> Nama Kursus</label>
            <input type="text" name="course_name" required class="form-input">
        </div>
        <div>
            <label class="block text-sm mb-1">Deskripsi</label>
            <textarea name="description" rows="3" class="form-input"></textarea>
        </div>
        <div>
            <label class="block text-sm mb-1">Foto Kursus</label>
            <input type="file" name="photo" accept="image/jpeg,image/png,image/gif,image/webp" class="form-input">
            <p class="text-xs text-slate-400 mt-0.5">Format: JPEG, PNG, GIF, WebP. Maks 2MB.</p>
        </div>
        <div class="flex gap-6 items-end">
            <div>
                <label class="block text-sm mb-1">Passing Score</label>
                <input type="number" name="passing_score" value="70" class="form-input w-24">
            </div>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="has_retest" class="rounded border-slate-300"> Ada Retest</label>
        </div>
    </div>

    <x-action-buttons align="left" class="p-4 border-t border-slate-200">
        <button type="submit" class="btn-primary">Simpan & Lanjut</button>
        <a href="{{ route('courses.index') }}" class="btn-secondary">Kembali</a>
    </x-action-buttons>
</form>
@endsection
