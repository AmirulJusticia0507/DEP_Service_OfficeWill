@extends('layouts.app')
@section('title', 'Activity Logs')
@section('header-icon')
    <i class="ti ti-history"></i>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 dark:text-slate-100 font-medium">Activity Logs</span>
@endsection
@section('content')
<h2 class="text-lg font-bold text-primary mb-4">Activity Logs</h2>

<div class="bg-white dark:bg-navy-800 rounded shadow mb-4">
    <div class="p-3 border-b border-slate-200 dark:border-b dark:border-slate-700">
        <form class="flex flex-wrap gap-2 items-end">
            <div>
                <label class="text-xs text-slate-500 dark:text-slate-300 block mb-0.5">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Description / IP / Employee" class="form-input w-64">
            </div>
            <div>
                <label class="text-xs text-slate-500 dark:text-slate-300 block mb-0.5">Action</label>
                <select name="action" class="form-input w-56">
                    <option value="">All actions</option>
                    @foreach ($actions as $a)
                        <option value="{{ $a }}" @selected(request('action') == $a)>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn-primary">Filter</button>
        </form>
    </div>
</div>

<div class="bg-white dark:bg-navy-800 shadow rounded overflow-x-auto">
    <table class="data-table w-full">
        <thead>
            <tr>
                <th>Time</th>
                <th>Employee</th>
                <th>Action</th>
                <th>Description</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td class="whitespace-nowrap text-xs">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $log->employee?->full_name ?? '-' }}</td>
                    <td><span class="px-2 py-0.5 rounded text-xs bg-slate-100 dark:bg-slate-700 font-mono">{{ $log->action }}</span></td>
                    <td class="text-sm max-w-md">{{ $log->description }}</td>
                    <td class="text-xs text-slate-500">{{ $log->ip_address ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-slate-400 dark:text-slate-500 py-6">No activity logs.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection
