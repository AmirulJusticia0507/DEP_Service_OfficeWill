@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="max-w-sm mx-auto mt-24">
    <div class="bg-white dark:bg-navy-800 shadow rounded p-8">
        <h1 class="text-xl font-bold text-center text-[#1e3a8a] mb-6">Masuk ke DEP Service</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="form-input @error('email') border-[#dc2626] @enderror">
                @error('email') <p class="text-[#dc2626] text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" required
                       class="form-input @error('password') border-[#dc2626] @enderror">
                @error('password') <p class="text-[#dc2626] text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="btn-primary w-full py-2">Masuk</button>
        </form>

        <hr class="my-4 border-slate-200 dark:border-slate-700">
        <form method="POST" action="{{ route('reissue-password') }}" class="text-center">
            @csrf
            <label class="block text-xs text-slate-500 dark:text-slate-300 mb-2">Lupa password? Masukkan email untuk reset:</label>
            <div class="flex gap-2">
                <input type="email" name="email" placeholder="email@domain.com" required class="form-input text-xs">
                <button type="submit" class="btn-primary text-xs whitespace-nowrap">Reset</button>
            </div>
        </form>
    </div>
</div>
@endsection
