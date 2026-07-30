@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="max-w-sm mx-auto mt-24">
    <div class="bg-white dark:bg-navy-800 shadow rounded p-8">
        <div class="text-center mb-6">
            <img src="/officewill_logo_yogya.svg" alt="OfficeWill" class="h-10 mx-auto">
            <p class="text-xs text-maroon-600 mt-2 font-medium">DEP Service</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="form-input @error('email') border-red-mark @enderror">
                @error('email') <p class="text-red-mark text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" required
                       class="form-input @error('password') border-red-mark @enderror">
                @error('password') <p class="text-red-mark text-xs mt-1">{{ $message }}</p> @enderror
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
