<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'DEP Service')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <div class="min-h-screen flex">
        @auth('employee')
        <aside class="w-64 bg-slate-900 text-white min-h-screen p-4 flex flex-col">
            <div class="text-lg font-bold mb-8 px-2">DEP Service</div>
            <nav class="flex-1 space-y-1">
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded hover:bg-slate-700 text-sm">Dashboard</a>
                <a href="{{ route('employees.index') }}" class="block px-3 py-2 rounded hover:bg-slate-700 text-sm">Karyawan</a>
                <a href="{{ route('courses.index') }}" class="block px-3 py-2 rounded hover:bg-slate-700 text-sm">Kursus</a>
                <a href="{{ route('enrollments.index') }}" class="block px-3 py-2 rounded hover:bg-slate-700 text-sm">Enrollment</a>
                <a href="{{ route('attendance.index') }}" class="block px-3 py-2 rounded hover:bg-slate-700 text-sm">Absensi Saya</a>
                <hr class="my-3 border-slate-700">
                <a href="{{ route('admin.assignments.index') }}" class="block px-3 py-2 rounded hover:bg-slate-700 text-sm">Penugasan</a>
                <a href="{{ route('admin.inquiries.course') }}" class="block px-3 py-2 rounded hover:bg-slate-700 text-sm">Inkuiri per Kursus</a>
                <a href="{{ route('admin.inquiries.employee') }}" class="block px-3 py-2 rounded hover:bg-slate-700 text-sm">Inkuiri per Karyawan</a>
            </nav>
            <div class="border-t border-slate-700 pt-3 space-y-1">
                <a href="{{ route('change-password') }}" class="block px-3 py-2 rounded hover:bg-slate-700 text-xs">Ganti Password</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded hover:bg-slate-700 text-xs text-rose-300">Logout</button>
                </form>
            </div>
        </aside>
        @endauth

        <main class="flex-1 p-6">
            @if (session('success'))
                <div class="bg-emerald-100 text-emerald-800 px-4 py-3 rounded mb-4 text-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-rose-100 text-rose-800 px-4 py-3 rounded mb-4 text-sm">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
