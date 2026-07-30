@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto mt-20">
    <div class="bg-white shadow rounded-lg p-8">
        <h1 class="text-2xl font-bold text-center mb-6">Masuk ke DEP Service</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border rounded px-3 py-2 text-sm @error('email') border-rose-400 @enderror">
                @error('email') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full border rounded px-3 py-2 text-sm @error('password') border-rose-400 @enderror">
                @error('password') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white rounded py-2 text-sm font-medium hover:bg-indigo-700">Masuk</button>
        </form>

        <hr class="my-4">
        <form method="POST" action="{{ route('reissue-password') }}" class="text-center">
            @csrf
            <label class="block text-xs text-slate-500 mb-1">Lupa password? Masukkan email untuk reset:</label>
            <div class="flex gap-2">
                <input type="email" name="email" placeholder="email@domain.com" required class="flex-1 border rounded px-2 py-1 text-xs">
                <button type="submit" class="bg-sky-600 text-white rounded px-3 py-1 text-xs hover:bg-sky-700">Reset</button>
            </div>
        </form>
    </div>
</div>
@endsection
