@extends('layouts.app')
@section('title', 'Certificate Verification')
@section('content')
<div class="max-w-lg mx-auto mt-16">
    @if(isset($valid) && $valid)
    <div class="bg-white rounded shadow p-8 text-center dark:bg-navy-800">
        <div class="text-emerald-500 text-5xl mb-4">✓</div>
        <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">Valid Certificate</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">This certificate is verified and authentic.</p>
        <div class="border-t border-slate-200 dark:border-slate-700 pt-4 space-y-2 text-sm text-left">
            <div class="flex justify-between"><span class="text-slate-500">{{ __('Employee') }}:</span><span class="font-medium">{{ $employee->full_name }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">{{ __('Course') }}:</span><span class="font-medium">{{ $course->course_name }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">{{ __('Certificate No') }}:</span><span class="font-medium">{{ $certificate->certificate_number }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">{{ __('Issued') }}:</span><span class="font-medium">{{ $certificate->issued_at->format('d M Y') }}</span></div>
        </div>
        <div class="mt-4">
            <img src="/officewill_logo_main.svg" alt="OfficeWill" class="h-8 mx-auto opacity-50">
        </div>
    </div>
    @else
    <div class="bg-white rounded shadow p-8 text-center dark:bg-navy-800">
        <div class="text-rose-500 text-5xl mb-4">✗</div>
        <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">Certificate Not Found</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">No certificate matches the provided number.</p>
        <form method="GET" action="{{ route('certificates.verify.form') }}" class="flex gap-2 max-w-sm mx-auto">
            <input type="text" name="certificate_number" placeholder="Enter certificate number" class="form-input" required>
            <button type="submit" class="btn-primary">{{ __('Verify') }}</button>
        </form>
    </div>
    @endif
</div>
@endsection
