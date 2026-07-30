@extends('layouts.app')
@section('title', 'Training Transcript')
@section('header-icon')<i class="ti ti-clipboard-list"></i>@endsection
@section('breadcrumbs')
    <a href="{{ route('profile.show') }}" class="text-slate-400 hover:text-slate-600">{{ __('My Profile') }}</a>
    <span class="mx-1">/</span>
    <span class="text-slate-800 font-medium dark:text-slate-100">{{ __('Training Transcript') }}</span>
@endsection
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded shadow p-6 dark:bg-navy-800">
        <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">{{ $employee->full_name }} — Training History</h2>

        @if($enrollments->isEmpty())
            <div class="text-center text-slate-400 py-8">{{ __('No data available') }}</div>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('Course') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Score') }}</th>
                            <th>{{ __('Deadline') }}</th>
                            <th>{{ __('Completed') }}</th>
                            <th>{{ __('Certificate') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enrollments as $en)
                        <tr>
                            <td class="font-medium">{{ $en->course->course_name }}</td>
                            <td>
                                @if($en->status === 'COMPLETED')
                                    <span class="status-badge status-badge-completed">{{ __('Completed') }}</span>
                                @elseif($en->status === 'ENROLLED')
                                    <span class="status-badge status-badge-in-progress">{{ __('In Progress') }}</span>
                                @else
                                    <span class="status-badge status-badge-pending">{{ __('Cancelled') }}</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $scores = $en->todoResponses->pluck('score')->filter();
                                @endphp
                                {{ $scores->isNotEmpty() ? $scores->avg() : '-' }}
                            </td>
                            <td class="text-xs">{{ $en->enrollment_deadline ? \Carbon\Carbon::parse($en->enrollment_deadline)->format('d M Y') : '-' }}</td>
                            <td class="text-xs">{{ $en->status === 'COMPLETED' ? $en->updated_at->format('d M Y') : '-' }}</td>
                            <td>
                                @if(isset($certificates[$en->course_id]))
                                    <a href="{{ route('certificates.download', $certificates[$en->course_id]) }}" class="text-gold-600 hover:underline text-xs flex items-center gap-1">
                                        <i class="ti ti-download"></i> {{ __('Download') }}
                                    </a>
                                @elseif($en->status === 'COMPLETED')
                                    <form method="POST" action="{{ route('certificates.generate', $en) }}">
                                        @csrf
                                        <button class="text-accent hover:underline text-xs flex items-center gap-1"><i class="ti ti-certificate"></i> Generate</button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
