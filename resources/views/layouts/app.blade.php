<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'DEP Service') — Office Will</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <div class="min-h-screen flex">
        @auth('employee')
        {{-- Sidebar Navigation --}}
        <aside class="w-60 bg-[#1E293B] text-white min-h-screen flex flex-col shrink-0">
            <div class="text-lg font-bold px-4 py-5 border-b border-slate-700">DEP Service</div>
            <nav class="flex-1 px-3 py-4 space-y-0.5 text-sm">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">Dashboard</a>
                <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">Karyawan</a>
                <a href="{{ route('courses.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">Kursus</a>
                <a href="{{ route('enrollments.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">Enrollment</a>
                <a href="{{ route('attendance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">Absensi Saya</a>
                <hr class="my-2 border-slate-700">
                <p class="px-3 text-[11px] text-slate-400 uppercase tracking-wider font-medium">Master Data</p>
                <a href="{{ route('admin.affiliations.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">Affiliation Master</a>
                <a href="{{ route('admin.positions.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">Position Master</a>
                <hr class="my-2 border-slate-700">
                <p class="px-3 text-[11px] text-slate-400 uppercase tracking-wider font-medium">Kursus</p>
                <a href="{{ route('admin.course-categories.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">Course Classification</a>
                <hr class="my-2 border-slate-700">
                <p class="px-3 text-[11px] text-slate-400 uppercase tracking-wider font-medium">Administrasi</p>
                <a href="{{ route('admin.assignments.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">Penugasan</a>
                <a href="{{ route('admin.inquiries.course') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">Inkuiri per Kursus</a>
                <a href="{{ route('admin.inquiries.employee') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">Inkuiri per Karyawan</a>
                <a href="{{ route('admin.inquiries.todo-answers') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700/60">ToDo Answer Inquiry</a>
            </nav>
            <div class="border-t border-slate-700 px-3 py-3 space-y-1 text-xs">
                <a href="{{ route('change-password') }}" class="block px-3 py-2 rounded hover:bg-slate-700/60">Ganti Password</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded hover:bg-slate-700/60 text-rose-300">Logout</button>
                </form>
            </div>
        </aside>

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col min-h-screen">
            {{-- Top Header Breadcrumb --}}
            <header class="bg-white border-b border-slate-200 px-6 py-3 flex items-center gap-2 text-sm text-slate-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                @hasSection('breadcrumbs')
                    @yield('breadcrumbs')
                @else
                    <span class="text-slate-800 font-medium">@yield('title', 'Dashboard')</span>
                @endif
            </header>

            {{-- Page Content --}}
            <main class="flex-1 p-6">
                @if (session('success'))
                    <div class="bg-emerald-100 text-emerald-800 px-4 py-3 rounded mb-4 text-sm border-l-4 border-emerald-500">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="bg-rose-100 text-rose-800 px-4 py-3 rounded mb-4 text-sm border-l-4 border-rose-500">{{ session('error') }}</div>
                @endif

                @yield('content')
            </main>
        </div>

        {{-- Floating Quick Menu (Right Bar) --}}
        @hasSection('quick-menu')
        <aside class="w-56 bg-white border-l border-slate-200 min-h-screen p-4 shrink-0">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Quick Menu</p>
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
</body>
</html>
