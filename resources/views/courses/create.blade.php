@extends('layouts.app')
@section('title', 'Tambah Kursus')
@section('content')
<h2 class="text-xl font-bold mb-4">Tambah Kursus</h2>
<form method="POST" class="max-w-xl bg-white shadow rounded p-6 space-y-3">
    @csrf
    <div>
        <label class="block text-sm mb-1">Kategori Detail</label>
        <select name="category_detail_id" class="w-full border rounded px-3 py-2 text-sm">
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
        <label class="block text-sm mb-1">Nama Kursus</label>
        <input type="text" name="course_name" required class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm mb-1">Deskripsi</label>
        <textarea name="description" rows="3" class="w-full border rounded px-3 py-2 text-sm"></textarea>
    </div>
    <div class="flex gap-4">
        <div>
            <label class="block text-sm mb-1">Passing Score</label>
            <input type="number" name="passing_score" value="70" class="w-24 border rounded px-2 py-2 text-sm">
        </div>
        <label class="flex items-center gap-2 text-sm mt-6"><input type="checkbox" name="has_retest"> Ada Retest</label>
    </div>
    <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-2 text-sm">Simpan & Lanjut</button>
</form>
@endsection
