<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'DEP Service') — Office Will</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function() {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="bg-slate-50 text-slate-900 antialiased dark:bg-navy-900 dark:text-slate-100">
    <div class="min-h-screen flex">
        @auth('employee')
        {{-- Sidebar Navigation --}}
        <aside class="w-60 bg-navy-800 text-white min-h-screen flex flex-col shrink-0 dark:bg-[#0f172a]">
            <div class="text-lg font-bold px-4 py-5 border-b border-slate-700">DEP Service</div>
            <nav class="flex-1 px-3 py-4 space-y-0.5 text-sm">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">{{ __('Dashboard') }}</a>
                <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">{{ __('Employees') }}</a>
                <a href="{{ route('courses.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">{{ __('Courses') }}</a>
                <a href="{{ route('enrollments.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">{{ __('Enrollments') }}</a>
                <a href="{{ route('attendance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">{{ __('Attendance') }}</a>
                <hr class="my-2 border-slate-700">
                <p class="px-3 text-[11px] text-slate-400 uppercase tracking-wider font-medium">{{ __('Master Data') }}</p>
                <a href="{{ route('admin.affiliations.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">{{ __('Affiliations') }}</a>
                <a href="{{ route('admin.positions.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">{{ __('Positions') }}</a>
                <hr class="my-2 border-slate-700">
                <p class="px-3 text-[11px] text-slate-400 uppercase tracking-wider font-medium">{{ __('Courses') }}</p>
                <a href="{{ route('admin.course-categories.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">{{ __('Classification') }}</a>
                <hr class="my-2 border-slate-700">
                <p class="px-3 text-[11px] text-slate-400 uppercase tracking-wider font-medium">{{ __('Administration') }}</p>
                <a href="{{ route('admin.assignments.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">{{ __('Assignments') }}</a>
                <a href="{{ route('admin.inquiries.course') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">{{ __('Inquiry by Course') }}</a>
                <a href="{{ route('admin.inquiries.employee') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">{{ __('Inquiry by Employee') }}</a>
                <a href="{{ route('admin.inquiries.todo-answers') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">{{ __('ToDo Answers') }}</a>
            </nav>
            <div class="border-t border-slate-700 px-3 py-3 space-y-1 text-xs">
                <button id="darkModeToggle" class="flex items-center gap-2 w-full px-3 py-2 rounded hover:bg-slate-700/60">
                    <span id="darkModeIcon">☀️</span>
                    <span id="darkModeLabel">{{ __('Dark Mode') }}</span>
                </button>
                <button id="langToggle" class="flex items-center gap-2 w-full px-3 py-2 rounded hover:bg-slate-700/60">
                    <span>🌐</span>
                    <span>{{ app()->getLocale() === 'ja' ? 'English' : '日本語' }}</span>
                </button>
                <form id="langForm" method="POST" action="{{ route('locale.switch') }}" class="hidden">
                    @csrf
                    <input type="hidden" name="locale" id="localeInput" value="">
                </form>
                <a href="{{ route('change-password') }}" class="block px-3 py-2 rounded hover:bg-slate-700/60">{{ __('Change Password') }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded hover:bg-slate-700/60 text-rose-300">{{ __('Logout') }}</button>
                </form>
            </div>
        </aside>

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col min-h-screen">
            {{-- Top Header Breadcrumb --}}
            <header class="bg-white border-b border-slate-200 px-6 py-3 flex items-center gap-2 text-sm text-slate-500 dark:bg-navy-800 dark:border-slate-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                @hasSection('breadcrumbs')
                    @yield('breadcrumbs')
                @else
                    <span class="text-slate-800 font-medium dark:text-slate-100">@yield('title', __('Dashboard'))</span>
                @endif
            </header>

            {{-- Page Content --}}
            <main class="flex-1 p-6">
                @if (session('success'))
                    <div class="bg-emerald-100 text-emerald-800 px-4 py-3 rounded mb-4 text-sm border-l-4 border-emerald-500 dark:bg-emerald-900 dark:text-emerald-200">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="bg-rose-100 text-rose-800 px-4 py-3 rounded mb-4 text-sm border-l-4 border-rose-500 dark:bg-rose-900 dark:text-rose-200">{{ session('error') }}</div>
                @endif

                @yield('content')
            </main>
        </div>

        {{-- Floating Quick Menu (Right Bar) --}}
        @hasSection('quick-menu')
        <aside class="w-56 bg-white border-l border-slate-200 min-h-screen p-4 shrink-0 dark:bg-navy-800 dark:border-slate-700">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">{{ __('Quick Menu') }}</p>
            <nav class="space-y-1">
                @yield('quick-menu')
            </nav>
        </aside>
        @endif
        @endauth

        @guest('employee')
            <main class="flex-1">
                @yield('content')
            </main>
        @endguest
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // ─── Dark Mode ───────────────────────────────────────────────────
        const toggle = document.getElementById('darkModeToggle');
        const icon = document.getElementById('darkModeIcon');
        const label = document.getElementById('darkModeLabel');

        function updateDarkUI() {
            const isDark = document.documentElement.classList.contains('dark');
            icon.textContent = isDark ? '☀️' : '🌙';
            if (label) label.textContent = isDark ? '{{ __("Light Mode") }}' : '{{ __("Dark Mode") }}';
        }

        if (toggle) {
            toggle.addEventListener('click', function() {
                document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
                updateDarkUI();
            });
        }
        updateDarkUI();

        // ─── Language Switcher ───────────────────────────────────────────
        const langBtn = document.getElementById('langToggle');
        const langForm = document.getElementById('langForm');
        const localeInput = document.getElementById('localeInput');

        if (langBtn && langForm) {
            langBtn.addEventListener('click', function() {
                const current = '{{ app()->getLocale() }}';
                localeInput.value = current === 'ja' ? 'en' : 'ja';
                langForm.submit();
            });
        }
    });
    </script>
</body>
</html>
