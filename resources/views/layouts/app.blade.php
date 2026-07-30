<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'DEP Service') — Office Will</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
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
            <nav class="flex-1 overflow-y-auto px-3 py-4 text-sm space-y-0.5">
                {{-- Single items --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">{{ __('Dashboard') }}</a>
                <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">{{ __('Employees') }}</a>
                <a href="{{ route('courses.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">{{ __('Courses') }}</a>
                <a href="{{ route('enrollments.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">{{ __('Enrollments') }}</a>
                <a href="{{ route('attendance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">{{ __('Attendance') }}</a>

                {{-- Master Data --}}
                <div class="tree-item">
                    <button class="tree-toggle flex items-center gap-2 w-full px-3 py-2 rounded hover:bg-slate-700/60 text-left text-[11px] text-slate-400 uppercase tracking-wider font-medium">
                        <svg class="tree-arrow w-3 h-3 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        {{ __('Master Data') }}
                    </button>
                    <div class="tree-children ml-3 space-y-0.5 hidden">
                        <a href="{{ route('admin.affiliations.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60 text-[13px]">{{ __('Affiliations') }}</a>
                        <a href="{{ route('admin.positions.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60 text-[13px]">{{ __('Positions') }}</a>
                        <a href="{{ route('admin.authorities.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60 text-[13px]">{{ __('Authorities') }}</a>
                    </div>
                </div>

                {{-- Course Classification --}}
                <div class="tree-item">
                    <button class="tree-toggle flex items-center gap-2 w-full px-3 py-2 rounded hover:bg-slate-700/60 text-left text-[11px] text-slate-400 uppercase tracking-wider font-medium">
                        <svg class="tree-arrow w-3 h-3 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        {{ __('Classification') }}
                    </button>
                    <div class="tree-children ml-3 space-y-0.5 hidden">
                        <a href="{{ route('admin.course-categories.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60 text-[13px]">{{ __('Classification') }}</a>
                    </div>
                </div>

                {{-- Administration --}}
                <div class="tree-item">
                    <button class="tree-toggle flex items-center gap-2 w-full px-3 py-2 rounded hover:bg-slate-700/60 text-left text-[11px] text-slate-400 uppercase tracking-wider font-medium">
                        <svg class="tree-arrow w-3 h-3 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        {{ __('Administration') }}
                    </button>
                    <div class="tree-children ml-3 space-y-0.5 hidden">
                        <a href="{{ route('admin.assignments.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60 text-[13px]">{{ __('Assignments') }}</a>
                        <a href="{{ route('admin.inquiries.course') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60 text-[13px]">{{ __('Inquiry by Course') }}</a>
                        <a href="{{ route('admin.inquiries.employee') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60 text-[13px]">{{ __('Inquiry by Employee') }}</a>
                        <a href="{{ route('admin.inquiries.todo-answers') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60 text-[13px]">{{ __('ToDo Answers') }}</a>
                    </div>
                </div>
            </nav>
            <div class="border-t border-slate-700 px-3 py-3 space-y-1 text-xs">
                <button id="darkModeToggle" class="flex items-center gap-2 w-full px-3 py-2 rounded hover:bg-slate-700/60">
                    <span id="darkModeIcon">☀️</span>
                    <span id="darkModeLabel">{{ __('Dark Mode') }}</span>
                </button>
                <button id="langToggle" class="flex items-center gap-2 w-full px-3 py-2 rounded hover:bg-slate-700/60">
                    <span>🌐</span>
                    <span id="langLabel">{{ locale_label(app()->getLocale()) }}</span>
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
                @hasSection('header-icon')
                    <span class="inline-flex items-center justify-center text-indigo-500 dark:text-indigo-400">@yield('header-icon')</span>
                @else
                    <span class="inline-flex items-center justify-center text-indigo-500 dark:text-indigo-400"><i class="ti ti-home"></i></span>
                @endif
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
        const langLabel = document.getElementById('langLabel');

        if (langBtn && langForm) {
            langBtn.addEventListener('click', function() {
                const current = '{{ app()->getLocale() }}';
                const locales = ['en', 'ja', 'id'];
                const idx = locales.indexOf(current);
                const next = locales[(idx + 1) % locales.length];
                localeInput.value = next;
                langForm.submit();
            });
        }

        // ─── Tree View (accordion) ──────────────────────────────────────
        document.querySelectorAll('.tree-toggle').forEach(function(btn) {
            const children = btn.nextElementSibling;
            const arrow = btn.querySelector('.tree-arrow');
            if (children) {
                // persist open state
                const key = 'tree_' + btoa(btn.textContent.trim());
                if (localStorage.getItem(key) === 'open') {
                    children.classList.remove('hidden');
                    if (arrow) arrow.classList.add('rotate-90');
                }
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    children.classList.toggle('hidden');
                    if (arrow) arrow.classList.toggle('rotate-90');
                    localStorage.setItem(key, children.classList.contains('hidden') ? 'closed' : 'open');
                });
            }
        });
    });
    </script>
</body>
</html>
