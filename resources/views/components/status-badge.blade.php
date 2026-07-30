@props(['status' => ''])

@php
    $classes = 'status-badge';
    if ($status === 'COMPLETED' || $status === 'PASSED' || $status === '修了') {
        $classes .= ' status-badge-completed';
    } elseif ($status === 'ENROLLED' || $status === 'IN_PROGRESS' || $status === '回答中') {
        $classes .= ' status-badge-in-progress';
    } else {
        $classes .= ' status-badge-pending';
    }
@endphp

<span class="{{ $classes }}">{{ $slot }}</span>
