@extends('emails.layout')
@section('title', 'Kursus Selesai')
@section('content')
    <h2 style="color:#380812;font-size:18px;margin:0 0 16px 0;">🎉 Selamat!</h2>
    <p>Halo <strong style="color:#380812;">{{ $employeeName }}</strong>,</p>
    <p>Anda telah berhasil menyelesaikan kursus:</p>

    <div style="background-color:#f0fdf4;border:1px solid #86efac;border-radius:6px;padding:16px;margin:16px 0;text-align:center;">
        <p style="font-size:16px;font-weight:700;color:#380812;margin:0 0 4px 0;">{{ $courseName }}</p>
        <p style="font-size:13px;color:#64748b;margin:0;">Status: <strong style="color:#16a34a;">LULUS</strong></p>
    </div>

    <p>Sertifikat Anda sudah tersedia. Klik tombol di bawah untuk mengunduh:</p>

    <p style="text-align:center;margin:24px 0;">
        <a href="{{ $certificateUrl }}" style="display:inline-block;background-color:#D4A017;color:#ffffff;padding:12px 32px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">Unduh Sertifikat</a>
    </p>

    <p style="font-size:13px;color:#94a3b8;">Terus kembangkan skill Anda dengan kursus lainnya!</p>
@endsection
