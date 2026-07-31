<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'DEP Service') — OfficeWill</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="alternate icon" href="/favicon.ico">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <script>
        (function() {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
            const savedColor = localStorage.getItem('theme-color');
            if (savedColor) {
                document.documentElement.setAttribute('data-theme-color', savedColor);
            }
        })();
    </script>
    <style id="theme-vars">
        :root, [data-theme-color="maroon"] {
            --sidebar-bg: #380812; --sidebar-hover: rgba(77,11,25,0.6); --sidebar-border: #4d0b19; --sidebar-dark: #1a0306;
            --sidebar-accent: #D4A017; --sidebar-accent-dim: rgba(212,160,23,0.6);
            --primary: #380812; --primary-dark: #2a050a; --accent: #D4A017; --accent-light: #dcb03e; --header-icon: #D4A017;
        }
        [data-theme-color="blue"] {
            --sidebar-bg: #1e3a8a; --sidebar-hover: rgba(30,64,175,0.6); --sidebar-border: #1d4ed8; --sidebar-dark: #172554;
            --sidebar-accent: #60a5fa; --sidebar-accent-dim: rgba(96,165,250,0.6);
            --primary: #1e3a8a; --primary-dark: #1e40af; --accent: #60a5fa; --accent-light: #93c5fd; --header-icon: #60a5fa;
        }
        [data-theme-color="green"] {
            --sidebar-bg: #065f46; --sidebar-hover: rgba(4,120,87,0.6); --sidebar-border: #047857; --sidebar-dark: #022c22;
            --sidebar-accent: #34d399; --sidebar-accent-dim: rgba(52,211,153,0.6);
            --primary: #065f46; --primary-dark: #047857; --accent: #34d399; --accent-light: #6ee7b7; --header-icon: #34d399;
        }
        [data-theme-color="purple"] {
            --sidebar-bg: #4c1d95; --sidebar-hover: rgba(91,33,182,0.6); --sidebar-border: #5b21b6; --sidebar-dark: #2e1065;
            --sidebar-accent: #a78bfa; --sidebar-accent-dim: rgba(167,139,250,0.6);
            --primary: #4c1d95; --primary-dark: #5b21b6; --accent: #a78bfa; --accent-light: #c4b5fd; --header-icon: #a78bfa;
        }
        [data-theme-color="red"] {
            --sidebar-bg: #991b1b; --sidebar-hover: rgba(185,28,28,0.6); --sidebar-border: #b91c1c; --sidebar-dark: #450a0a;
            --sidebar-accent: #f87171; --sidebar-accent-dim: rgba(248,113,113,0.6);
            --primary: #991b1b; --primary-dark: #b91c1c; --accent: #f87171; --accent-light: #fca5a5; --header-icon: #f87171;
        }
        [data-theme-color="slate"] {
            --sidebar-bg: #1e293b; --sidebar-hover: rgba(51,65,85,0.6); --sidebar-border: #334155; --sidebar-dark: #0f172a;
            --sidebar-accent: #94a3b8; --sidebar-accent-dim: rgba(148,163,184,0.6);
            --primary: #1e293b; --primary-dark: #334155; --accent: #94a3b8; --accent-light: #cbd5e1; --header-icon: #94a3b8;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900 antialiased dark:bg-navy-900 dark:text-slate-100">
    <div class="min-h-screen flex">
        @auth('employee')
        {{-- Sidebar Navigation --}}
        <aside id="sidebar" class="w-60 bg-sidebar text-white min-h-screen flex flex-col shrink-0 border-r border-sidebar" style="background-color: var(--sidebar-bg); border-color: var(--sidebar-border);">
            <div class="flex items-center gap-2 px-4 py-4 border-b border-sidebar">
                <img src="/officewill_logo_yogya.svg" alt="OfficeWill" class="officewill-brand-logo">
            </div>
            @php
                $u = auth('employee')->user();
                $isMasterData = request()->routeIs('admin.affiliations.*') || request()->routeIs('admin.positions.*') || request()->routeIs('admin.authorities.*');
                $isClassification = request()->routeIs('admin.course-categories.*');
                $isAdministration = request()->routeIs('admin.assignments.*') || request()->routeIs('admin.inquiries.*') || request()->routeIs('admin.exam-reports.*') || request()->routeIs('admin.mail-log.*');
            @endphp
            <nav class="flex-1 overflow-y-auto px-3 py-4 text-sm space-y-0.5">
                <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-2 px-3 py-2 rounded sidebar-hover {{ request()->routeIs('dashboard') ? 'sidebar-active' : '' }}"><i class="ti ti-layout-dashboard text-sidebar-accent"></i> {{ __('Dashboard') }}</a>
                @if($u->is_sys_admin || $u->can_register_employee)
                <a href="{{ route('employees.index') }}" class="sidebar-link flex items-center gap-2 px-3 py-2 rounded sidebar-hover {{ request()->routeIs('employees.*') ? 'sidebar-active' : '' }}"><i class="ti ti-users text-sidebar-accent"></i> {{ __('Employees') }}</a>
                @endif
                @if($u->is_sys_admin || $u->can_register_course)
                <a href="{{ route('courses.index') }}" class="sidebar-link flex items-center gap-2 px-3 py-2 rounded sidebar-hover {{ request()->routeIs('courses.*') ? 'sidebar-active' : '' }}"><i class="ti ti-book text-sidebar-accent"></i> {{ __('Courses') }}</a>
                @endif
                @if($u->is_sys_admin || $u->can_register_course)
                <a href="{{ route('enrollments.index') }}" class="sidebar-link flex items-center gap-2 px-3 py-2 rounded sidebar-hover {{ request()->routeIs('enrollments.*') ? 'sidebar-active' : '' }}"><i class="ti ti-clipboard-list text-sidebar-accent"></i> {{ __('Enrollments') }}</a>
                @endif
                @if($u->is_sys_admin || $u->can_setting_attendance)
                <a href="{{ route('attendance.index') }}" class="sidebar-link flex items-center gap-2 px-3 py-2 rounded sidebar-hover {{ request()->routeIs('attendance.*') ? 'sidebar-active' : '' }}"><i class="ti ti-calendar-check text-sidebar-accent"></i> {{ __('Attendance') }}</a>
                @endif
                @if($u->is_sys_admin)
                <a href="{{ route('admin.mail-log.index') }}" class="sidebar-link flex items-center gap-2 px-3 py-2 rounded sidebar-hover {{ request()->routeIs('admin.mail-log.*') ? 'sidebar-active' : '' }}"><i class="ti ti-mail text-sidebar-accent"></i> {{ __('Mail Log') }}</a>
                @endif
                <a href="{{ route('profile.show') }}" class="sidebar-link flex items-center gap-2 px-3 py-2 rounded sidebar-hover {{ request()->routeIs('profile.*') ? 'sidebar-active' : '' }}"><i class="ti ti-user-circle text-sidebar-accent"></i> {{ __('My Profile') }}</a>

                @if($u->is_sys_admin)
                <div class="tree-item mt-2">
                    <button class="tree-toggle flex items-center gap-2 w-full px-3 py-2 rounded sidebar-hover text-left text-[11px] text-sidebar-accent uppercase tracking-wider font-medium">
                        <svg class="tree-arrow w-3 h-3 shrink-0 transition-transform duration-200 {{ $isMasterData ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <i class="ti ti-database"></i> {{ __('Master Data') }}
                    </button>
                    <div class="tree-children ml-3 space-y-0.5 {{ $isMasterData ? '' : 'hidden' }}">
                        <a href="{{ route('admin.affiliations.index') }}" class="sidebar-link flex items-center gap-2 px-3 py-2 rounded sidebar-hover text-[13px] {{ request()->routeIs('admin.affiliations.*') ? 'sidebar-active' : '' }}"><i class="ti ti-building-community text-sidebar-accent-dim"></i> {{ __('Affiliations') }}</a>
                        <a href="{{ route('admin.positions.index') }}" class="sidebar-link flex items-center gap-2 px-3 py-2 rounded sidebar-hover text-[13px] {{ request()->routeIs('admin.positions.*') ? 'sidebar-active' : '' }}"><i class="ti ti-briefcase text-sidebar-accent-dim"></i> {{ __('Positions') }}</a>
                        <a href="{{ route('admin.authorities.index') }}" class="sidebar-link flex items-center gap-2 px-3 py-2 rounded sidebar-hover text-[13px] {{ request()->routeIs('admin.authorities.*') ? 'sidebar-active' : '' }}"><i class="ti ti-shield text-sidebar-accent-dim"></i> {{ __('Authorities') }}</a>
                    </div>
                </div>
                @endif

                @if($u->is_sys_admin)
                <div class="tree-item">
                    <button class="tree-toggle flex items-center gap-2 w-full px-3 py-2 rounded sidebar-hover text-left text-[11px] text-sidebar-accent uppercase tracking-wider font-medium">
                        <svg class="tree-arrow w-3 h-3 shrink-0 transition-transform duration-200 {{ $isClassification ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <i class="ti ti-category"></i> {{ __('Classification') }}
                    </button>
                    <div class="tree-children ml-3 space-y-0.5 {{ $isClassification ? '' : 'hidden' }}">
                        <a href="{{ route('admin.course-categories.index') }}" class="sidebar-link flex items-center gap-2 px-3 py-2 rounded sidebar-hover text-[13px] {{ request()->routeIs('admin.course-categories.*') ? 'sidebar-active' : '' }}"><i class="ti ti-tags text-sidebar-accent-dim"></i> {{ __('Classification') }}</a>
                    </div>
                </div>
                @endif

                @if($u->is_sys_admin || $u->can_register_course)
                <div class="tree-item">
                    <button class="tree-toggle flex items-center gap-2 w-full px-3 py-2 rounded sidebar-hover text-left text-[11px] text-sidebar-accent uppercase tracking-wider font-medium">
                        <svg class="tree-arrow w-3 h-3 shrink-0 transition-transform duration-200 {{ $isAdministration ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <i class="ti ti-settings"></i> {{ __('Administration') }}
                    </button>
                    <div class="tree-children ml-3 space-y-0.5 {{ $isAdministration ? '' : 'hidden' }}">
                        <a href="{{ route('admin.assignments.index') }}" class="sidebar-link flex items-center gap-2 px-3 py-2 rounded sidebar-hover text-[13px] {{ request()->routeIs('admin.assignments.*') ? 'sidebar-active' : '' }}"><i class="ti ti-mail-forward text-sidebar-accent-dim"></i> {{ __('Assignments') }}</a>
                        <a href="{{ route('admin.inquiries.course') }}" class="sidebar-link flex items-center gap-2 px-3 py-2 rounded sidebar-hover text-[13px] {{ request()->routeIs('admin.inquiries.course') ? 'sidebar-active' : '' }}"><i class="ti ti-search text-sidebar-accent-dim"></i> {{ __('Inquiry by Course') }}</a>
                        <a href="{{ route('admin.inquiries.employee') }}" class="sidebar-link flex items-center gap-2 px-3 py-2 rounded sidebar-hover text-[13px] {{ request()->routeIs('admin.inquiries.employee') ? 'sidebar-active' : '' }}"><i class="ti ti-search text-sidebar-accent-dim"></i> {{ __('Inquiry by Employee') }}</a>
                        <a href="{{ route('admin.inquiries.todo-answers') }}" class="sidebar-link flex items-center gap-2 px-3 py-2 rounded sidebar-hover text-[13px] {{ request()->routeIs('admin.inquiries.todo-answers') ? 'sidebar-active' : '' }}"><i class="ti ti-list-details text-sidebar-accent-dim"></i> {{ __('ToDo Answers') }}</a>
                        <a href="{{ route('admin.exam-reports.by-course') }}" class="sidebar-link flex items-center gap-2 px-3 py-2 rounded sidebar-hover text-[13px] {{ request()->routeIs('admin.exam-reports.*') ? 'sidebar-active' : '' }}"><i class="ti ti-report-analytics text-sidebar-accent-dim"></i> {{ __('Exam Reports') }}</a>
                    </div>
                </div>
                @endif
            </nav>
            <div class="border-t border-sidebar px-3 py-3 space-y-1 text-xs">
                <button id="darkModeToggle" class="flex items-center gap-2 w-full px-3 py-2 rounded sidebar-hover">
                    <span id="darkModeIcon">☀️</span>
                    <span id="darkModeLabel">{{ __('Dark Mode') }}</span>
                </button>
                <button id="langToggle" class="flex items-center gap-2 w-full px-3 py-2 rounded sidebar-hover">
                    <span>🌐</span>
                    <span id="langLabel">{{ locale_label(app()->getLocale()) }}</span>
                </button>
                <form id="langForm" method="POST" action="{{ route('locale.switch') }}" class="hidden">
                    @csrf
                    <input type="hidden" name="locale" id="localeInput" value="">
                </form>
                {{-- Theme Swatches --}}
                <div class="flex items-center gap-2 px-3 py-1">
                    <span class="text-[10px] opacity-60">Theme:</span>
                    <button class="theme-dot w-4 h-4 rounded-full border border-white/30" data-theme="maroon" style="background:#380812" title="Maroon"></button>
                    <button class="theme-dot w-4 h-4 rounded-full border border-white/30" data-theme="blue" style="background:#1e3a8a" title="Blue"></button>
                    <button class="theme-dot w-4 h-4 rounded-full border border-white/30" data-theme="green" style="background:#065f46" title="Green"></button>
                    <button class="theme-dot w-4 h-4 rounded-full border border-white/30" data-theme="purple" style="background:#4c1d95" title="Purple"></button>
                    <button class="theme-dot w-4 h-4 rounded-full border border-white/30" data-theme="red" style="background:#991b1b" title="Red"></button>
                    <button class="theme-dot w-4 h-4 rounded-full border border-white/30" data-theme="slate" style="background:#1e293b" title="Slate"></button>
                </div>
                <a href="{{ route('change-password') }}" class="flex items-center gap-2 px-3 py-2 rounded sidebar-hover"><i class="ti ti-lock"></i> {{ __('Change Password') }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 w-full text-left px-3 py-2 rounded sidebar-hover text-rose-300"><i class="ti ti-logout"></i> {{ __('Logout') }}</button>
                </form>
            </div>
        </aside>

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col min-h-screen">
            {{-- Top Header with Notification Bell --}}
            <header class="bg-white border-b border-slate-200 px-6 py-3 flex items-center justify-between text-sm text-slate-500 dark:bg-navy-800 dark:border-slate-700">
                <div class="flex items-center gap-2">
                    @hasSection('header-icon')
                        <span class="inline-flex items-center justify-center text-header-icon">@yield('header-icon')</span>
                    @else
                        <span class="inline-flex items-center justify-center text-header-icon"><i class="ti ti-home"></i></span>
                    @endif
                    @hasSection('breadcrumbs')
                        @yield('breadcrumbs')
                    @else
                        <span class="text-slate-800 font-medium dark:text-slate-100">@yield('title', __('Dashboard'))</span>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <span class="branch-label-tag">Yogyakarta</span>
                    {{-- Notification Bell --}}
                    <div class="relative" id="notifContainer">
                        <button id="notifBell" class="icon-btn relative" title="{{ __('Notifications') }}">
                            <i class="ti ti-bell text-base"></i>
                            <span id="notifBadge" class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[9px] w-4 h-4 flex items-center justify-center rounded-full font-bold hidden"></span>
                        </button>
                        <div id="notifDropdown" class="notification-dropdown hidden">
                            <div class="flex items-center justify-between px-4 py-2 border-b border-slate-100 dark:border-slate-700">
                                <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">{{ __('Notifications') }}</span>
                                <button id="markAllRead" class="text-[10px] text-header-icon hover:underline">{{ __('Mark all read') }}</button>
                            </div>
                            <div id="notifList">
                                <div class="notification-item text-center text-slate-400 py-6">{{ __('No notifications') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 p-6">
                @if (session('success'))
                    <div class="bg-emerald-100 text-emerald-800 px-4 py-3 rounded mb-4 text-sm border-l-4 border-emerald-500 dark:bg-emerald-900 dark:text-emerald-200">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="bg-rose-100 text-rose-800 px-4 py-3 rounded mb-4 text-sm border-l-4 border-rose-500 dark:bg-rose-900 dark:text-rose-200">{{ session('error') }}</div>
                @endif
                @if (session('info'))
                    <div class="bg-sky-100 text-sky-800 px-4 py-3 rounded mb-4 text-sm border-l-4 border-sky-500 dark:bg-sky-900 dark:text-sky-200">{{ session('info') }}</div>
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
            if (icon) icon.textContent = isDark ? '☀️' : '🌙';
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

        // ─── Notifications ──────────────────────────────────────────────
        const notifBell = document.getElementById('notifBell');
        const notifDropdown = document.getElementById('notifDropdown');

        function toggleNotif(e) {
            e.stopPropagation();
            notifDropdown.classList.toggle('hidden');
            if (!notifDropdown.classList.contains('hidden')) {
                fetchNotifs();
            }
        }

        if (notifBell) {
            notifBell.addEventListener('click', toggleNotif);
            document.addEventListener('click', function(e) {
                if (!document.getElementById('notifContainer').contains(e.target)) {
                    notifDropdown.classList.add('hidden');
                }
            });
        }

        function fetchNotifs() {
            fetch('/notifications')
                .then(r => r.json())
                .then(data => {
                    const list = document.getElementById('notifList');
                    const badge = document.getElementById('notifBadge');
                    if (data.length === 0) {
                        list.innerHTML = '<div class="notification-item text-center text-slate-400 py-6">{{ __("No notifications") }}</div>';
                        badge.classList.add('hidden');
                        return;
                    }
                    const unread = data.filter(n => !n.is_read).length;
                    if (unread > 0) {
                        badge.textContent = unread > 9 ? '9+' : unread;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                    list.innerHTML = data.map(n => `
                        <div class="notification-item ${n.is_read ? '' : 'unread'}" data-id="${n.id}" onclick="markRead(${n.id})">
                            <p class="text-xs text-slate-800 dark:text-slate-100">${n.message}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">${n.created_at}</p>
                        </div>
                    `).join('');
                });
        }

        window.markRead = function(id) {
            fetch('/notifications/' + id + '/read', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' } })
                .then(() => fetchNotifs());
        };

        document.getElementById('markAllRead')?.addEventListener('click', function() {
            fetch('/notifications/read-all', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' } })
                .then(() => fetchNotifs());
        });

        @auth('employee')
        // Poll every 30s
        setInterval(fetchNotifs, 30000);
        @endauth

        // ─── Theme Color Switcher ────────────────────────────────────────
        document.querySelectorAll('.theme-dot').forEach(function(dot) {
            dot.addEventListener('click', function() {
                const theme = this.getAttribute('data-theme');
                document.documentElement.setAttribute('data-theme-color', theme);
                localStorage.setItem('theme-color', theme);
            });
        });
    });
    </script>
</body>
</html>
