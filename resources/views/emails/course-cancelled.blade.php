@extends('emails.layout')
@section('title', 'Kursus Dibatalkan')
@section('content')
    <h2 style="color:#380812;font-size:18px;margin:0 0 16px 0;">Kursus Dibatalkan</h2>
    <p>Halo <strong style="color:#380812;">{{ $employeeName }}</strong>,</p>
    <p>Kursus <strong>{{ $courseName }}</strong> yang sebelumnya ditugaskan kepada Anda telah <strong style="color:#dc2626;">dibatalkan</strong>.</p>
    <p style="font-size:13px;color:#64748b;">Jika ada pertanyaan, silakan hubungi admin.</p>
@endsection
