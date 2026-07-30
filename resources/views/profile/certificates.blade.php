@extends('layouts.app')
@section('title', 'My Certificates')
@section('header-icon')<i class="ti ti-certificate"></i>@endsection
@section('breadcrumbs')
    <a href="{{ route('profile.show') }}" class="text-slate-400 hover:text-slate-600">{{ __('My Profile') }}</a>
    <span class="mx-1">/</span>
    <span class="text-slate-800 font-medium dark:text-slate-100">{{ __('Certificates') }}</span>
@endsection
@section('content')
<div class="max-w-3xl mx-auto">
    @if($certificates->isEmpty())
    <div class="bg-white rounded shadow p-8 text-center text-slate-400 dark:bg-navy-800 dark:text-slate-500">{{ __('No data available') }}</div>
    @else
    <div class="space-y-3">
        @foreach($certificates as $cert)
        <div class="bg-white rounded shadow p-4 flex items-center justify-between dark:bg-navy-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded bg-maroon-100 dark:bg-maroon-900 flex items-center justify-center text-maroon-700 dark:text-gold-400">
                    <i class="ti ti-certificate text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $cert->course->course_name }}</p>
                    <p class="text-xs text-slate-400">{{ $cert->certificate_number }} · {{ $cert->issued_at->format('d M Y') }}</p>
                </div>
            </div>
            <a href="{{ route('certificates.download', $cert) }}" class="btn-gold text-xs flex items-center gap-1">
                <i class="ti ti-download"></i> {{ __('Download') }}
            </a>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
