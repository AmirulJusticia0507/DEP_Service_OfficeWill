# DEP Service - Education & Course Management System

Sistem Manajemen Pelatihan dan Kehadiran Karyawan berbasis web.

## Tech Stack
- Laravel 13.x + Blade + Tailwind CSS v4
- MySQL 8.0+ | PHP ^8.3 | Node ^22

## Setup
```bash
composer install
npm install
cp .env.example .env   # isi DB credentials
php artisan key:generate
php artisan migrate
php artisan storage:link
npm run build
php artisan serve
```

## Fitur
- Login & Password Management (account locking 5x error)
- Manajemen Karyawan & Afiliasi bertingkat (Pusat/Franchise)
- Scope-based authority (ONLY / BELOW / ALL)
- Master Kursus & Kategori (Video YouTube & PDF)
- Enrollment & Attendance Screen
- Post-Course Todo (Kuesioner, Laporan, Ujian/Scoring)
- Penugasan massal + Email notification
- Inkuiri per Kursus / per Karyawan

## Struktur
Lihat [docs/Guideline.md](docs/Guideline.md) untuk detail arsitektur.
