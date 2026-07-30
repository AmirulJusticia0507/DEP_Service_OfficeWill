@extends('layouts.app')
@section('title', 'Authority Management')
@section('header-icon')
    <i class="ti ti-shield"></i>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 dark:text-slate-100 font-medium">Authority Management</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">User Roles</span>
@endsection
@section('content')
<h2 class="text-lg font-bold text-[#1e3a8a] mb-4">Authority & Role Management</h2>

<div class="bg-white dark:bg-navy-800 rounded shadow mb-4">
    <div class="p-3 border-b border-slate-200 dark:border-b dark:border-slate-700">
        <form class="flex flex-wrap gap-2 items-end">
            <div>
                <label class="text-xs text-slate-500 dark:text-slate-300 block mb-0.5">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name / Code / Email" class="form-input w-64">
            </div>
            <button class="btn-primary">Filter</button>
        </form>
    </div>
</div>

<div class="bg-white dark:bg-navy-800 shadow rounded overflow-x-auto">
    <table class="data-table w-full">
        <thead>
            <tr>
                <th>Employee</th>
                <th class="text-center">Sys Admin</th>
                <th class="text-center">Register Emp</th>
                <th class="text-center">Register Course</th>
                <th class="text-center">Set Attendance</th>
                <th>Authority Scope</th>
                <th>Affil. Code</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $emp)
            <tr>
                <form method="POST" action="{{ route('admin.authorities.update', $emp) }}">
                    @csrf @method('PUT')
                    <td>
                        <div class="font-medium">{{ $emp->full_name }}</div>
                        <div class="text-xs text-slate-400">{{ $emp->employee_code }} · {{ $emp->email }}</div>
                    </td>
                    <td class="text-center">
                        <input type="checkbox" name="is_sys_admin" value="1" @checked($emp->is_sys_admin) class="rounded border-slate-300 dark:border-slate-600 dark:bg-navy-800">
                    </td>
                    <td class="text-center">
                        <input type="checkbox" name="can_register_employee" value="1" @checked($emp->can_register_employee) class="rounded border-slate-300 dark:border-slate-600 dark:bg-navy-800">
                    </td>
                    <td class="text-center">
                        <input type="checkbox" name="can_register_course" value="1" @checked($emp->can_register_course) class="rounded border-slate-300 dark:border-slate-600 dark:bg-navy-800">
                    </td>
                    <td class="text-center">
                        <input type="checkbox" name="can_setting_attendance" value="1" @checked($emp->can_setting_attendance) class="rounded border-slate-300 dark:border-slate-600 dark:bg-navy-800">
                    </td>
                    <td>
                        <select name="authority_effective_range" class="form-select text-xs">
                            <option value="ONLY" @selected($emp->authority_effective_range === 'ONLY')>Own</option>
                            <option value="BELOW" @selected($emp->authority_effective_range === 'BELOW')>Own & Sub</option>
                            <option value="ALL" @selected($emp->authority_effective_range === 'ALL')>All</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="authority_effective_affiliation_code" value="{{ $emp->authority_effective_affiliation_code }}" class="form-input w-20 text-xs">
                    </td>
                    <td>
                        <button class="btn-primary text-xs px-2 py-1">Save</button>
                    </td>
                </form>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $employees->links() }}</div>
@endsection
