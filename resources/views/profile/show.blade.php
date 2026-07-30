@extends('layouts.app')
@section('title', 'My Profile')
@section('header-icon')<i class="ti ti-user-circle"></i>@endsection
@section('breadcrumbs')
    <span class="text-slate-800 font-medium dark:text-slate-100">{{ __('My Profile') }}</span>
@endsection
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded shadow dark:bg-navy-800 overflow-hidden">
        <div class="bg-primary p-6 text-white">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-maroon-500 flex items-center justify-center text-2xl font-bold overflow-hidden">
                    @if($employee->photo)
                        <img src="{{ Storage::url($employee->photo) }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($employee->full_name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <h1 class="text-xl font-bold">{{ $employee->full_name }}</h1>
                    <p class="text-sm text-gold-300">{{ $employee->email }}</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Profile Details</h2>
                <a href="{{ route('profile.edit') }}" class="btn-primary text-xs"><i class="ti ti-pencil"></i> {{ __('Edit') }}</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-slate-400 text-xs">{{ __('Employee') }} Code</span>
                    <p class="font-medium">{{ $employee->employee_code ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-slate-400 text-xs">{{ __('Name') }} (Kana)</span>
                    <p class="font-medium">{{ $employee->kana_name ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-slate-400 text-xs">{{ __('Phone') }}</span>
                    <p class="font-medium">{{ $employee->phone_number ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-slate-400 text-xs">{{ __('Email') }}</span>
                    <p class="font-medium">{{ $employee->email }}</p>
                </div>
                <div>
                    <span class="text-slate-400 text-xs">{{ __('Place of Birth') }}</span>
                    <p class="font-medium">{{ $employee->place_of_birth ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-slate-400 text-xs">{{ __('Date of Birth') }}</span>
                    <p class="font-medium">{{ $employee->date_of_birth ? $employee->date_of_birth->format('d M Y') : '-' }}</p>
                </div>
                <div>
                    <span class="text-slate-400 text-xs">{{ __('Gender') }}</span>
                    <p class="font-medium">{{ $employee->gender ? __($employee->gender === 'MALE' ? 'Male' : 'Female') : '-' }}</p>
                </div>
                <div>
                    <span class="text-slate-400 text-xs">{{ __('Status') }}</span>
                    <p class="font-medium">{{ $employee->account_status ?? 'ACTIVE' }}</p>
                </div>
            </div>

            @if($employee->address)
            <div class="mt-4">
                <span class="text-slate-400 text-xs">Address</span>
                <p class="text-sm">{{ $employee->address }}</p>
            </div>
            @endif

            @if($employee->employeeAffiliations->isNotEmpty())
            <div class="mt-6">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">Affiliations</h3>
                @foreach($employee->employeeAffiliations as $ea)
                <div class="text-sm flex gap-2 {{ $ea->end_date ? 'text-slate-400' : '' }}">
                    <span>{{ $ea->affiliation->affiliation_name ?? '-' }}</span>
                    @if($ea->end_date)
                    <span class="text-[10px]">({{ $ea->start_date?->format('d M Y') }} - {{ $ea->end_date?->format('d M Y') }})</span>
                    @else
                    <span class="text-[10px] text-emerald-500">Active</span>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            <div class="mt-6 flex items-center gap-3">
                <a href="{{ route('profile.transcript') }}" class="btn-secondary text-xs"><i class="ti ti-clipboard-list"></i> {{ __('Training Transcript') }}</a>
                <a href="{{ route('profile.certificates') }}" class="btn-secondary text-xs"><i class="ti ti-certificate"></i> {{ __('Certificates') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
