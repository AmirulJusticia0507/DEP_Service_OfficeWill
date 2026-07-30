@extends('emails.layout')
@section('title', 'Akun Terkunci')
@section('content')
    <h2 style="color:#380812;font-size:18px;margin:0 0 16px 0;">Akun Terkunci</h2>
    <p>Halo <strong style="color:#380812;">{{ $employeeName }}</strong>,</p>

    <div style="background-color:#fef2f2;border:1px solid #fca5a5;border-radius:6px;padding:16px;margin:16px 0;">
        <p style="margin:0;font-size:14px;color:#dc2626;">
            Akun DEP Service Anda ({{ $email }}) telah dikunci karena 5 kali gagal login.
        </p>
    </div>

    <p>Untuk membuka kembali akun Anda, silakan hubungi admin atau gunakan fitur <strong>Reset Password</strong> di halaman login.</p>

    <p style="text-align:center;margin:24px 0;">
        <a href="{{ config('app.url') }}/login" style="display:inline-block;background-color:#380812;color:#ffffff;padding:12px 32px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">Ke Halaman Login</a>
    </p>
@endsection
