@extends('emails.layout')
@section('title', 'Kode Verifikasi Login')
@section('content')
    <h2 style="color:#380812;font-size:18px;margin:0 0 16px 0;">Kode Verifikasi Login</h2>
    <p>Halo <strong style="color:#380812;">{{ $employeeName }}</strong>,</p>
    <p>Gunakan kode verifikasi berikut untuk menyelesaikan login ke akun DEP Service Anda. Kode berlaku selama <strong style="color:#D4A017;">{{ $expiresInMinutes }} menit</strong>.</p>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0;">
        <tr>
            <td align="center" style="background-color:#F8FAFC;border:1px solid #D4A017;border-radius:8px;padding:24px;">
                <span style="font-size:32px;font-weight:700;letter-spacing:8px;color:#380812;">{{ $otp }}</span>
            </td>
        </tr>
    </table>

    <p style="font-size:12px;color:#94a3b8;">Jika Anda tidak merasa melakukan login, abaikan email ini dan hubungi admin untuk mengamankan akun Anda.</p>
@endsection
