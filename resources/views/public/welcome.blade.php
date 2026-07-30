<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DEP Service — Office Will</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
</head>
<body class="bg-gradient-to-br from-slate-900 via-navy-800 to-slate-900 text-white min-h-screen flex flex-col">
    <div class="flex-1 flex items-center justify-center px-4">
        <div class="max-w-lg text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-500/20 text-indigo-400 mb-6">
                <i class="ti ti-building-skyscraper text-3xl"></i>
            </div>
            <h1 class="text-4xl font-bold mb-2">DEP Service</h1>
            <p class="text-slate-400 text-lg mb-1">Office Will</p>
            <p class="text-slate-500 text-sm mb-8">Employee Training & Development Management System</p>
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg font-medium transition-colors">
                <i class="ti ti-login"></i>
                Login
            </a>
            <div class="mt-12 grid grid-cols-3 gap-4 text-center text-sm">
                <div class="bg-white/5 rounded-lg p-4">
                    <i class="ti ti-book text-indigo-400 text-xl block mb-1"></i>
                    <span class="text-slate-400">Course<br>Management</span>
                </div>
                <div class="bg-white/5 rounded-lg p-4">
                    <i class="ti ti-users text-indigo-400 text-xl block mb-1"></i>
                    <span class="text-slate-400">Employee<br>Management</span>
                </div>
                <div class="bg-white/5 rounded-lg p-4">
                    <i class="ti ti-calendar-check text-indigo-400 text-xl block mb-1"></i>
                    <span class="text-slate-400">Attendance<br>Tracking</span>
                </div>
            </div>
        </div>
    </div>
    <footer class="text-center text-slate-600 text-xs py-4">
        &copy; {{ date('Y') }} Office Will. All rights reserved.
    </footer>
</body>
</html>
