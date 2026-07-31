@extends('layouts.app')
@section('title', 'Exam - ' . $todo->title)
@section('header-icon')<i class="ti ti-edit"></i>@endsection
@section('breadcrumbs')
    <a href="{{ route('attendance.index') }}" class="text-slate-400 hover:text-slate-600">{{ __('Attendance') }}</a>
    <span class="mx-1">/</span>
    <span class="text-slate-800 font-medium dark:text-slate-100">{{ $todo->title }}</span>
@endsection
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded shadow p-6 dark:bg-navy-800 mb-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ $todo->title }}</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $todo->description }}</p>
            </div>
            <div class="text-right">
                <div id="timer" class="text-2xl font-mono font-bold text-accent">--:--</div>
                <p class="text-[10px] text-slate-400 mt-0.5">sisa waktu</p>
            </div>
        </div>
        <div class="flex items-center gap-3 mt-3 text-xs text-slate-400">
            <span>Attempt #{{ $attempt->attempt_number }}</span>
            <span>Max Score: {{ $attempt->max_score }}</span>
            <span>{{ $questions->count() }} soal</span>
        </div>
    </div>

    <form method="POST" action="{{ route('exam.submit', $attempt) }}" id="examForm">
        @csrf
        <div class="space-y-4">
            @foreach($questions as $index => $q)
            <div class="bg-white rounded shadow p-5 dark:bg-navy-800">
                <div class="flex items-start gap-2 mb-3">
                    <span class="text-xs font-bold text-accent bg-primary-10 w-6 h-6 rounded-full flex items-center justify-center shrink-0">{{ $index + 1 }}</span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $q->question_text }}</p>
                        <span class="text-[10px] text-slate-400">{{ $q->points }} pts | {{ $q->question_type }}</span>
                    </div>
                </div>

                @if($q->question_type === 'MCQ')
                    <div class="space-y-2 ml-8">
                        @foreach($q->options as $opt)
                        <label class="flex items-center gap-2 text-sm cursor-pointer p-2 rounded hover:bg-slate-50 dark:hover:bg-navy-900">
                            <input type="radio" name="question_{{ $q->id }}" value="{{ $opt->id }}" class="accent-primary" required>
                            {{ $opt->option_text }}
                        </label>
                        @endforeach
                    </div>
                @elseif($q->question_type === 'TRUE_FALSE')
                    <div class="space-y-2 ml-8">
                        @foreach($q->options as $opt)
                        <label class="flex items-center gap-2 text-sm cursor-pointer p-2 rounded hover:bg-slate-50 dark:hover:bg-navy-900">
                            <input type="radio" name="question_{{ $q->id }}" value="{{ $opt->id }}" class="accent-primary" required>
                            {{ $opt->option_text }}
                        </label>
                        @endforeach
                    </div>
                @else
                    <div class="ml-8">
                        <textarea name="question_{{ $q->id }}" rows="4" class="form-input" placeholder="Tulis jawaban Anda..."></textarea>
                    </div>
                @endif
            </div>
            @endforeach
        </div>

        <div class="flex items-center justify-between gap-2 mt-6 bg-white rounded shadow p-4 dark:bg-navy-800 sticky bottom-0">
            <span id="timerFooter" class="text-sm font-mono text-accent font-semibold"></span>
            <div class="flex items-center gap-2">
                <a href="{{ route('attendance.score', $enrollment) }}" class="btn-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn-gold py-2 px-6">{{ __('Submit Answer') }}</button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('examForm');
    const timerEl = document.getElementById('timer');
    const timerFooter = document.getElementById('timerFooter');

    // ─── Timer: 60 minutes ────────────────────────────────────
    const DURATION = 60 * 60; // seconds
    let remaining = DURATION;

    function pad(n) { return String(n).padStart(2, '0'); }

    function formatTime(sec) {
        const m = Math.floor(sec / 60);
        const s = sec % 60;
        return pad(m) + ':' + pad(s);
    }

    function updateTimer() {
        const display = formatTime(remaining);
        timerEl.textContent = display;
        if (timerFooter) timerFooter.textContent = '⏱ ' + display;

        if (remaining <= 300 && remaining > 0) {
            timerEl.classList.add('text-rose-500');
            timerEl.classList.remove('text-accent');
        }

        if (remaining <= 0) {
            timerEl.textContent = '00:00';
            if (timerFooter) timerFooter.textContent = '⏱ Waktu habis!';
            form.submit();
        }
    }

    const saved = localStorage.getItem('exam_timer_' + '{{ $attempt->id }}');
    if (saved) {
        const elapsed = Math.floor((Date.now() - parseInt(saved)) / 1000);
        remaining = Math.max(0, DURATION - elapsed);
    } else {
        localStorage.setItem('exam_timer_' + '{{ $attempt->id }}', Date.now().toString());
    }

    updateTimer();
    const interval = setInterval(function() {
        remaining--;
        updateTimer();
        if (remaining <= 0) clearInterval(interval);
    }, 1000);

    // Clear saved timer on submit
    form.addEventListener('submit', function() {
        localStorage.removeItem('exam_timer_' + '{{ $attempt->id }}');
    });

    // ─── Warn before leaving ──────────────────────────────────
    let dirty = false;
    form.querySelectorAll('input, textarea').forEach(el => {
        el.addEventListener('change', () => dirty = true);
    });
    window.addEventListener('beforeunload', function(e) {
        if (dirty) { e.preventDefault(); e.returnValue = ''; }
    });
    form.addEventListener('submit', () => dirty = false);
});
</script>
@endsection
