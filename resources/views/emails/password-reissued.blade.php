@extends('emails.layout')
@section('title', 'Password Direset')
@section('content')
    <h2 style="color:#380812;font-size:18px;margin:0 0 16px 0;">Password Direset</h2>
    <p>Halo <strong style="color:#380812;">{{ $employeeName }}</strong>,</p>
    <p>Password akun DEP Service Anda telah di-reset. Gunakan password baru berikut untuk login:</p>

    <table style="border:1px solid #D4A017;border-radius:6px;padding:16px;margin:16px 0;width:100%;">
        <tr>
            <td style="padding:6px 12px;font-size:13px;color:#64748b;width:120px;">Password Baru</td>
            <td style="padding:6px 12px;font-size:14px;font-weight:600;color:#380812;font-family:monospace;">{{ $newPassword }}</td>
        </tr>
    </table>

    <p style="text-align:center;margin:24px 0;">
        <a href="{{ $loginUrl }}" style="display:inline-block;background-color:#380812;color:#ffffff;padding:12px 32px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">Login ke DEP Service</a>
    </p>
@endsection
