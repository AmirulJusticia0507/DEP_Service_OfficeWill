@extends('emails.layout')
@section('title', 'Password Berhasil Diubah')
@section('content')
    <h2 style="color:#380812;font-size:18px;margin:0 0 16px 0;">Password Berhasil Diubah</h2>
    <p>Halo <strong style="color:#380812;">{{ $employeeName }}</strong>,</p>
    <p>Password akun DEP Service Anda telah berhasil diubah.</p>
    <div style="background-color:#f0fdf4;border:1px solid #86efac;border-radius:6px;padding:12px 16px;margin:16px 0;font-size:13px;color:#166534;">
        Jika Anda tidak melakukan perubahan ini, segera hubungi admin.
    </div>
@endsection
