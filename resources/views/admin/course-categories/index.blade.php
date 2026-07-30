@extends('layouts.app')
@section('title', 'Course Classification')
@section('breadcrumbs')
    <span class="text-slate-800 font-medium">Course Registration</span>
    <span class="text-slate-400 mx-1">/</span>
    <span class="text-slate-800 font-medium">Course Classification</span>
@endsection
@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-bold text-[#1e3a8a]">Course Classification</h2>
    <a href="{{ route('admin.course-categories.create') }}" class="btn-primary">+ Add New Classification</a>
</div>

<div class="space-y-6">
    @forelse ($categories as $cat)
    <div class="bg-white shadow rounded">
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200">
            <div>
                <strong class="text-sm">{{ $cat->category_name }}</strong>
                <span class="text-xs text-slate-400 ml-2">({{ $cat->category_code }})</span>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.course-categories.edit', $cat) }}" class="text-[#1e3a8a] hover:underline text-xs font-medium">Edit</a>
                <form method="POST" action="{{ route('admin.course-categories.destroy', $cat) }}" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                    @csrf @method('DELETE')
                    <button class="text-[#dc2626] hover:underline text-xs">Delete</button>
                </form>
            </div>
        </div>

        <div class="p-4">
            <form method="POST" action="{{ route('admin.course-categories.details.store', $cat) }}" class="flex flex-wrap gap-2 mb-4 items-end">
                @csrf
                <div>
                    <label class="text-xs text-slate-500 block mb-0.5">Detail Code</label>
                    <input type="text" name="detail_code" placeholder="Code" required class="form-input w-28">
                </div>
                <div>
                    <label class="text-xs text-slate-500 block mb-0.5">Detail Name</label>
                    <input type="text" name="detail_name" placeholder="Name" required class="form-input w-48">
                </div>
                <div>
                    <label class="text-xs text-slate-500 block mb-0.5">Order</label>
                    <input type="number" name="display_order" placeholder="0" class="form-input w-16">
                </div>
                <button class="btn-primary">Add</button>
            </form>

            @if($cat->details->count())
            <table class="data-table">
                <thead><tr><th>Code</th><th>Name</th><th>Order</th><th></th></tr></thead>
                <tbody>
                    @foreach ($cat->details as $det)
                    <tr>
                        <td>{{ $det->detail_code }}</td>
                        <td>{{ $det->detail_name }}</td>
                        <td>{{ $det->display_order }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.course-categories.details.destroy', $det) }}" class="inline" onsubmit="return confirm('Hapus detail ini?')">
                                @csrf @method('DELETE')
                                <button class="text-[#dc2626] hover:underline text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-sm text-slate-400">Belum ada detail kategori.</p>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white shadow rounded p-6 text-center text-slate-400">Tidak ada kategori.</div>
    @endforelse
</div>

<div class="mt-4">{{ $categories->links() }}</div>
@endsection
