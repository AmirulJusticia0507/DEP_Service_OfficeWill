<!DOCTYPE html>
<html><body style="font-family:sans-serif;padding:24px">
<h2>Akun DEP Service</h2>
<p>Halo <strong>{{ $employeeName }}</strong>,</p>
<p>Akun DEP Service Anda telah dibuat. Silakan login menggunakan credential berikut:</p>
<table style="border:1px solid #ddd;padding:12px;margin:12px 0">
    <tr><td style="padding:4px 8px">Email</td><td><strong>{{ $email }}</strong></td></tr>
    <tr><td style="padding:4px 8px">Password</td><td><strong>{{ $password }}</strong></td></tr>
</table>
<p><a href="{{ $loginUrl }}" style="background:#4f46e5;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none">Login ke DEP Service</a></p>
<p>Kami sarankan untuk mengganti password setelah login pertama.</p>
</body></html>
