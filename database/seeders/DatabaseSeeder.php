<?php

namespace Database\Seeders;

use App\Models\Affiliation;
use App\Models\Company;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseCategoryDetail;
use App\Models\CourseEnrollment;
use App\Models\CourseMaterial;
use App\Models\CourseTodo;
use App\Models\Employee;
use App\Models\EmployeeAffiliation;
use App\Models\MasterJob;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Company ──────────────────────────────────────────────────────────
        $company = Company::firstOrCreate(
            ['company_name' => 'PT Demo Center'],
            ['login_url' => 'http://localhost:8000/login']
        );

        // ─── Affiliations ──────────────────────────────────────────────────────
        $affiliations = [
            ['code' => 'PST', 'name' => 'Kantor Pusat', 'order' => 1, 'type' => 1, 'parent' => null],
            ['code' => 'FC-001', 'name' => 'FC Cabang 1', 'order' => 2, 'type' => 2, 'parent' => 'PST'],
            ['code' => 'FC-002', 'name' => 'FC Cabang 2', 'order' => 3, 'type' => 2, 'parent' => 'PST'],
            ['code' => 'HRD', 'name' => 'Divisi SDM', 'order' => 4, 'type' => 1, 'parent' => 'PST'],
            ['code' => 'IT', 'name' => 'Divisi IT', 'order' => 5, 'type' => 1, 'parent' => 'PST'],
            ['code' => 'FIN', 'name' => 'Divisi Keuangan', 'order' => 6, 'type' => 1, 'parent' => 'PST'],
        ];

        foreach ($affiliations as $a) {
            Affiliation::firstOrCreate(
                ['company_id' => $company->id, 'affiliation_code' => $a['code']],
                [
                    'affiliation_name' => $a['name'],
                    'display_order' => $a['order'],
                    'organization_type' => $a['type'],
                    'parent_affiliation_code' => $a['parent'],
                ]
            );
        }

        // ─── Master Jobs ──────────────────────────────────────────────────────
        $jobs = [
            ['id' => 'ADM', 'title' => 'Administrator', 'order' => 1],
            ['id' => 'MGR', 'title' => 'Manager', 'order' => 2],
            ['id' => 'SPV', 'title' => 'Supervisor', 'order' => 3],
            ['id' => 'STF', 'title' => 'Staff', 'order' => 4],
            ['id' => 'DIR', 'title' => 'Director', 'order' => 5],
        ];

        foreach ($jobs as $j) {
            MasterJob::firstOrCreate(
                ['company_id' => $company->id, 'job_id' => $j['id']],
                [
                    'job_title' => $j['title'],
                    'display_order' => $j['order'],
                ]
            );
        }

        // ─── Admin Employee ────────────────────────────────────────────────────
        $admin = Employee::firstOrCreate(
            ['company_id' => $company->id, 'employee_code' => 'ADM001'],
            [
                'full_name' => 'Admin Demo',
                'kana_name' => 'アドミン',
                'email' => 'admin@demo.com',
                'password' => Hash::make('password123'),
                'account_status' => 'ACTIVE',
                'is_sys_admin' => true,
                'can_register_employee' => true,
                'can_register_course' => true,
                'can_setting_attendance' => true,
                'authority_effective_range' => 'ALL',
            ]
        );

        EmployeeAffiliation::firstOrCreate(
            ['employee_id' => $admin->id, 'affiliation_code' => 'PST'],
            [
                'company_id' => $company->id,
                'job_id' => 'ADM',
                'start_date' => now(),
            ]
        );

        // ─── Additional Employees ──────────────────────────────────────────────
        $employees = [
            ['code' => 'EMP001', 'name' => 'Budi Santoso', 'email' => 'budi@demo.com', 'aff' => 'HRD', 'job' => 'MGR'],
            ['code' => 'EMP002', 'name' => 'Siti Rahayu', 'email' => 'siti@demo.com', 'aff' => 'IT', 'job' => 'STF'],
            ['code' => 'EMP003', 'name' => 'Ahmad Fauzi', 'email' => 'ahmad@demo.com', 'aff' => 'FIN', 'job' => 'SPV'],
            ['code' => 'EMP004', 'name' => 'Dewi Lestari', 'email' => 'dewi@demo.com', 'aff' => 'FC-001', 'job' => 'STF'],
        ];

        foreach ($employees as $e) {
            $emp = Employee::firstOrCreate(
                ['company_id' => $company->id, 'employee_code' => $e['code']],
                [
                    'full_name' => $e['name'],
                    'email' => $e['email'],
                    'password' => Hash::make('password123'),
                    'account_status' => 'ACTIVE',
                    'authority_effective_range' => 'ONLY',
                    'authority_effective_affiliation_code' => $e['aff'],
                ]
            );
            EmployeeAffiliation::firstOrCreate(
                ['employee_id' => $emp->id, 'affiliation_code' => $e['aff']],
                [
                    'company_id' => $company->id,
                    'job_id' => $e['job'],
                    'start_date' => now(),
                ]
            );
        }

        // ─── Course Categories ─────────────────────────────────────────────────
        $categories = [
            ['code' => 'COMMON', 'name' => 'Common', 'order' => 1, 'details' => [
                ['code' => 'C-IND', 'name' => 'Induction', 'order' => 1],
                ['code' => 'C-COMP', 'name' => 'Compliance', 'order' => 2],
                ['code' => 'C-ETC', 'name' => 'Etiquette', 'order' => 3],
            ]],
            ['code' => 'PROF', 'name' => 'Profession', 'order' => 2, 'details' => [
                ['code' => 'P-TECH', 'name' => 'Technical', 'order' => 1],
                ['code' => 'P-MGT', 'name' => 'Management', 'order' => 2],
                ['code' => 'P-FIN', 'name' => 'Finance', 'order' => 3],
            ]],
            ['code' => 'TECH', 'name' => 'Teknis', 'order' => 3, 'details' => [
                ['code' => 'TCH-OPR', 'name' => 'Operasional Teknis', 'order' => 1],
                ['code' => 'TCH-DEV', 'name' => 'Pengembangan', 'order' => 2],
            ]],
        ];

        foreach ($categories as $catData) {
            $cat = CourseCategory::firstOrCreate(
                ['category_code' => $catData['code']],
                ['category_name' => $catData['name'], 'display_order' => $catData['order']]
            );
            foreach ($catData['details'] as $detData) {
                CourseCategoryDetail::firstOrCreate(
                    ['category_id' => $cat->id, 'detail_code' => $detData['code']],
                    ['detail_name' => $detData['name'], 'display_order' => $detData['order']]
                );
            }
        }

        // ─── Sample Courses ────────────────────────────────────────────────────
        $samples = [
            [
                'detail_code' => 'C-IND',
                'name' => 'Pelatihan Awal Karyawan Baru',
                'desc' => 'Pengenalan budaya perusahaan dan prosedur dasar',
                'score' => 70,
                'has_retest' => true,
                'materials' => [
                    ['type' => 'YOUTUBE', 'title' => 'Video Pengenalan Perusahaan', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'order' => 1],
                    ['type' => 'PDF', 'title' => 'Buku Panduan Karyawan', 'url' => '/samples/panduan.pdf', 'order' => 2],
                ],
                'todos' => [
                    ['type' => 'QUESTIONNAIRE', 'title' => 'Survey Kepuasan', 'desc' => 'Berikan tanggapan Anda', 'score' => null, 'order' => 1],
                    ['type' => 'TEST', 'title' => 'Ujian Pemahaman Dasar', 'desc' => 'Uji pemahaman Anda', 'score' => 70, 'order' => 2],
                ],
            ],
            [
                'detail_code' => 'P-TECH',
                'name' => 'Pelatihan Keamanan IT',
                'desc' => 'Keamanan siber dan perlindungan data',
                'score' => 75,
                'has_retest' => false,
                'materials' => [
                    ['type' => 'YOUTUBE', 'title' => 'Video Keamanan IT', 'url' => 'https://www.youtube.com/watch?v=example', 'order' => 1],
                ],
                'todos' => [
                    ['type' => 'TEST', 'title' => 'Ujian Keamanan IT', 'desc' => 'Tes pemahaman keamanan', 'score' => 75, 'order' => 1],
                    ['type' => 'REPORT', 'title' => 'Laporan Evaluasi', 'desc' => 'Tulis laporan evaluasi', 'score' => null, 'order' => 2],
                ],
            ],
        ];

        foreach ($samples as $s) {
            $detail = CourseCategoryDetail::where('detail_code', $s['detail_code'])->first();
            if (!$detail) continue;

            $course = Course::firstOrCreate(
                ['course_name' => $s['name']],
                [
                    'category_detail_id' => $detail->id,
                    'description' => $s['desc'],
                    'passing_score' => $s['score'],
                    'has_retest' => $s['has_retest'],
                ]
            );

            foreach ($s['materials'] as $m) {
                CourseMaterial::firstOrCreate(
                    ['course_id' => $course->id, 'title' => $m['title']],
                    [
                        'material_type' => $m['type'],
                        'content_url_or_path' => $m['url'],
                        'display_order' => $m['order'],
                    ]
                );
            }

            foreach ($s['todos'] as $t) {
                CourseTodo::firstOrCreate(
                    ['course_id' => $course->id, 'title' => $t['title']],
                    [
                        'todo_type' => $t['type'],
                        'description' => $t['desc'],
                        'display_order' => $t['order'],
                        'passing_score' => $t['score'],
                    ]
                );
            }
        }

        // ─── Enrollments ──────────────────────────────────────────────────────
        $firstCourse = Course::where('course_name', 'Pelatihan Awal Karyawan Baru')->first();
        if ($firstCourse) {
            $allEmps = Employee::where('company_id', $company->id)->get();
            foreach ($allEmps as $emp) {
                CourseEnrollment::firstOrCreate(
                    ['course_id' => $firstCourse->id, 'employee_id' => $emp->id],
                    [
                        'enrollment_deadline' => now()->addDays(30),
                        'status' => 'ENROLLED',
                    ]
                );
            }
        }
    }
}
