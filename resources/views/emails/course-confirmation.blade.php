@extends('emails.layout')
@section('title', 'Konfirmasi Pendaftaran Kursus')
@section('content')
    <h2 style="color:#380812;font-size:18px;margin:0 0 16px 0;">Konfirmasi Pendaftaran Kursus</h2>
    <p>Halo <strong style="color:#380812;">{{ $employeeName }}</strong>,</p>
    <p>Pendaftaran dan konfirmasi keikutsertaan Anda dalam kursus berikut telah berhasil diproses:</p>

    <table style="border:1px solid #D4A017;border-radius:6px;padding:16px;margin:16px 0;width:100%;">
        <tr>
            <td style="padding:6px 12px;font-size:13px;color:#64748b;width:120px;">Nama Kursus</td>
            <td style="padding:6px 12px;font-size:14px;font-weight:600;color:#380812;">{{ $courseName }}</td>
        </tr>
        <tr>
            <td style="padding:6px 12px;font-size:13px;color:#64748b;">Status Konfirmasi</td>
            <td style="padding:6px 12px;font-size:14px;font-weight:600;color:#059669;">{{ $status }}</td>
        </tr>
        <tr>
            <td style="padding:6px 12px;font-size:13px;color:#64748b;">Batas Waktu</td>
            <td style="padding:6px 12px;font-size:14px;font-weight:600;color:#D4A017;">{{ $deadline }}</td>
        </tr>
    </table>

    <p style="font-size:13px;color:#475569;margin-bottom:20px;">
        Silakan klik tombol di bawah ini untuk mengakses materi pembelajaran dan mencatat kehadiran Anda:
    </p>

    <p style="text-align:center;margin:24px 0;">
        <a href="{{ $courseUrl }}" style="display:inline-block;background-color:#380812;color:#ffffff;padding:12px 32px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">Buka Halaman Kursus</a>
    </p>
@endsection
