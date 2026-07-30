<!DOCTYPE html>
<html><body style="font-family:sans-serif;padding:24px">
<h2>Penugasan Kursus Baru</h2>
<p>Halo <strong>{{ $employeeName }}</strong>,</p>
<p>Anda telah ditugaskan untuk mengikuti kursus:</p>
<table style="border:1px solid #ddd;padding:12px;margin:12px 0">
    <tr><td style="padding:4px 8px">Kursus</td><td><strong>{{ $courseName }}</strong></td></tr>
    <tr><td style="padding:4px 8px">Batas Waktu</td><td><strong>{{ $deadline }}</strong></td></tr>
</table>
<p><a href="{{ $courseUrl }}" style="background:#4f46e5;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none">Mulai Kursus</a></p>
</body></html>
