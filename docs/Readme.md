# DEP Service - Education & Course Management System

Sistem Manajemen Pelatihan dan Kehadiran Karyawan (**DEP Service**) berbasis web yang dirancang untuk mengelola materi pembelajaran (Video YouTube & PDF), evaluasi post-course (Kuesioner, Laporan, dan Ujian/Scoring), pengelolaan unit kerja/afiliasi bertingkat (Pusat & Franchise), serta kontrol cakupan wewenang operator (*Operator Authority Scope*).

---

## 🛠️ Tech Stack & Persyaratan Sistem

- **Framework**: Laravel 13.x (Fullstack Blade + Alpine.js / Livewire)
- **CSS Framework**: Tailwind CSS v4.x
- **Database**: MySQL 8.0+
- **PHP**: ^8.3
- **Node.js**: ^22.0 (untuk Vite & Tailwind build)

---

## 🚀 Fitur Utama Sistem

### 1. Sistem Keamanan & Otentikasi
- **Login & Password Management**: Validasi akun aktif, fitur ganti password, dan reissued password.
- **Account Locking System**: Akun terkunci otomatis jika salah memasukkan password sebanyak **5 kali berturut-turut** (mencatat waktu pencatatan akun terkunci).

### 2. Manajemen Karyawan & Master Data
- **Affiliation Master**: Pengelolaan hirarki unit kerja/cabang (Kantor Pusat vs Store FC/Franchise) lengkap dengan urutan tampilan.
- **Job Title / Position Master**: Pengelolaan jabatan karyawan.
- **Employee Management**: Pengelolaan data karyawan, tanggal aktif/berakhir afiliasi, serta penetapan hak akses khusus.

### 3. Kontrol Cakupan Wewenang Operator (*Valid Scope of Operator Authority*)
Sistem mengontrol visibilitas data karyawan dan kursus berdasarkan rentang afiliasi operator:
- `Affiliation only`: Hanya melihat data pada afiliasinya sendiri.
- `Below the affiliation`: Melihat data afiliasinya dan seluruh sub-afiliasi di bawahnya.
- `All affiliations`: Melihat seluruh data afiliasi tanpa batasan.

### 4. Manajemen Master Kursus & Kategori
- **Course Classification & Details**: Pengelolaan kategori dan sub-kategori pelatihan.
- **Course Management**: Pendaftaran materi kursus, pengaturan skor kelulusan (*passing score*), status ujian ulang (*retest*), dan pengelolaan materi (Embed YouTube & PDF Viewer).

### 5. Pelaksanaan & Penilaian Kursus (Student Side)
- **Employee Course List**: Dashboard siswa untuk melihat kursus yang terdaftar & batas waktu (*enrollment deadline*).
- **Attendance Screen**: Halaman materi pembelajaran (Video YouTube & PDF viewer internal).
- **Post-Course ToDo & Test Scoring**: Penyelenggaraan kuesioner, pengumpulan laporan, dan ujian interaktif dengan pengujian otomatis (*pass/fail threshold* dan *retest loop*).

### 6. Pengaturan & Inkuiri Kursus (Admin / Instructor Side)
- **Course Assignment / Settings**: Penugasan kursus ke karyawan/afiliasi secara massal dengan *enrollment deadline* dan pengiriman notifikasi email otomatis.
- **Course & Employee Inquiry**: Pemantauan progres kehadiran dan hasil ToDo berdasarkan kursus maupun berdasarkan karyawan.

### 7. Sistem Email Dinamis (*External Template File*)
Semua subjek dan isi email didefinisikan secara eksternal (menggunakan Blade Email Views/Template config) sehingga dapat diperbarui tanpa mengubah kode program:
- Email Notifikasi Registrasi Akun
- Email Notifikasi Reissue Password
- Email Konfirmasi Perubahan Password
- Email Penugasan Kursus
- Email Pembatalan Kursus

---

## 📊 Skema Database (MySQL ERD Overview)

```mermaid
erDiagram
    COMPANIES ||--o{ AFFILIATIONS : has
    COMPANIES ||--o{ JOBS : has
    COMPANIES ||--o{ EMPLOYEES : employs
    EMPLOYEES ||--o{ EMPLOYEE_AFFILIATIONS : assigns
    AFFILIATIONS ||--o{ EMPLOYEE_AFFILIATIONS : belongs
    JOBS ||--o{ EMPLOYEE_AFFILIATIONS : holds
    COURSE_CATEGORIES ||--o{ COURSE_CATEGORY_DETAILS : contains
    COURSE_CATEGORY_DETAILS ||--o{ COURSES : classifies
    COURSES ||--o{ COURSE_MATERIALS : includes
    COURSES ||--o{ COURSE_TODOS : defines
    COURSES ||--o{ COURSE_ENROLLMENTS : assigned_to
    EMPLOYEES ||--o{ COURSE_ENROLLMENTS : enrolled
    COURSE_ENROLLMENTS ||--o{ COURSE_TODO_RESPONSES : submits