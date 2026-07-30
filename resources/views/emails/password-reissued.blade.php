<!DOCTYPE html>
<html><body style="font-family:sans-serif;padding:24px">
<h2>Password Direset</h2>
<p>Halo <strong>{{ $employeeName }}</strong>,</p>
<p>Password akun DEP Service Anda telah di-reset. Gunakan password baru berikut untuk login:</p>
<table style="border:1px solid #ddd;padding:12px;margin:12px 0">
    <tr><td style="padding:4px 8px">Password Baru</td><td><strong>{{ $newPassword }}</strong></td></tr>
</table>
<p><a href="{{ $loginUrl }}" style="background:#4f46e5;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none">Login ke DEP Service</a></p>
</body></html>
