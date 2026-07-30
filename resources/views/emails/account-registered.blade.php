@extends('emails.layout')
@section('title', 'Akun DEP Service')
@section('content')
    <h2 style="color:#380812;font-size:18px;margin:0 0 16px 0;">Akun Berhasil Dibuat</h2>
    <p>Halo <strong style="color:#380812;">{{ $employeeName }}</strong>,</p>
    <p>Akun DEP Service Anda telah dibuat. Silakan login menggunakan credential berikut:</p>

    <table style="border:1px solid #D4A017;border-radius:6px;padding:16px;margin:16px 0;width:100%;">
        <tr>
            <td style="padding:6px 12px;font-size:13px;color:#64748b;width:100px;">Email</td>
            <td style="padding:6px 12px;font-size:14px;font-weight:600;color:#380812;">{{ $email }}</td>
        </tr>
        <tr>
            <td style="padding:6px 12px;font-size:13px;color:#64748b;">Password</td>
            <td style="padding:6px 12px;font-size:14px;font-weight:600;color:#380812;font-family:monospace;">{{ $password }}</td>
        </tr>
    </table>

    <p style="text-align:center;margin:24px 0;">
        <a href="{{ $loginUrl }}" style="display:inline-block;background-color:#380812;color:#ffffff;padding:12px 32px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">Login ke DEP Service</a>
    </p>

    <p style="font-size:13px;color:#94a3b8;border-top:1px solid #e2e8f0;padding-top:16px;">Kami sarankan untuk mengganti password setelah login pertama.</p>
@endsection
