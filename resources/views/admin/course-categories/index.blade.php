@extends('layouts.app')
@section('title', 'Course Classification')
@section('header-icon')
    <i class="ti ti-folder"></i>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 dark:text-slate-100 font-medium">Course Registration</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">Course Classification</span>
@endsection
@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-bold text-primary">Course Classification</h2>
    <a href="{{ route('admin.course-categories.create') }}" class="btn-primary">+ Add New Classification</a>
</div>

<div class="space-y-6">
    @forelse ($categories as $cat)
    <div class="bg-white dark:bg-navy-800 shadow rounded overflow-hidden">
        <div class="tree-toggle flex items-center justify-between px-4 py-3 cursor-pointer border-b border-slate-200 dark:border-b dark:border-slate-700 select-none">
            <div class="flex items-center gap-2">
                <svg class="tree-arrow w-4 h-4 shrink-0 transition-transform duration-200 {{ $loop->first ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                @if($cat->icon)
                    <img src="{{ asset('storage/' . $cat->icon) }}" class="w-6 h-6 object-contain">
                @endif
                <strong class="text-sm">{{ $cat->category_name }}</strong>
                <span class="text-xs text-slate-400 dark:text-slate-500 ml-2">({{ $cat->category_code }})</span>
            </div>
            <div class="flex items-center gap-2" onclick="event.stopPropagation()">
                <a href="{{ route('admin.course-categories.edit', $cat) }}" class="btn-sm btn-sm-edit inline-block">Edit</a>
                <form method="POST" action="{{ route('admin.course-categories.destroy', $cat) }}" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-sm btn-sm-delete">Delete</button>
                </form>
            </div>
        </div>

        <div class="tree-children p-4 {{ $loop->first ? '' : 'hidden' }}">
            <form method="POST" action="{{ route('admin.course-categories.details.store', $cat) }}" enctype="multipart/form-data" class="flex flex-wrap gap-2 mb-4 items-end">
                @csrf
                <div>
                    <label class="text-xs text-slate-500 dark:text-slate-300 block mb-0.5">Detail Code</label>
                    <input type="text" name="detail_code" placeholder="Code" required class="form-input w-28">
                </div>
                <div>
                    <label class="text-xs text-slate-500 dark:text-slate-300 block mb-0.5">Detail Name</label>
                    <input type="text" name="detail_name" placeholder="Name" required class="form-input w-48">
                </div>
                <div>
                    <label class="text-xs text-slate-500 dark:text-slate-300 block mb-0.5">Order</label>
                    <input type="number" name="display_order" placeholder="0" class="form-input w-16">
                </div>
                <div>
                    <label class="text-xs text-slate-500 dark:text-slate-300 block mb-0.5">Icon</label>
                    <input type="file" name="icon" accept="image/png,image/jpg,image/jpeg,image/svg+xml" class="form-input text-xs w-28">
                </div>
                <button class="btn-primary">Add</button>
            </form>

            @if($cat->details->count())
            <table class="data-table">
                <thead><tr><th>Code</th><th>Name</th><th>Order</th><th>Icon</th><th></th></tr></thead>
                <tbody>
                    @foreach ($cat->details as $det)
                    <tr>
                        <td>{{ $det->detail_code }}</td>
                        <td>{{ $det->detail_name }}</td>
                        <td>{{ $det->display_order }}</td>
                        <td>
                            @if($det->icon)
                                <img src="{{ asset('storage/' . $det->icon) }}" class="w-6 h-6 object-contain">
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.course-categories.details.destroy', $det) }}" class="inline" onsubmit="return confirm('Hapus detail ini?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-500 hover:underline text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada detail kategori.</p>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white dark:bg-navy-800 shadow rounded p-6 text-center text-slate-400 dark:text-slate-500">Tidak ada kategori.</div>
    @endforelse
</div>

<div class="mt-4">{{ $categories->links() }}</div>
@endsection
