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
        $company = Company::create([
            'company_name' => 'PT Demo Center',
            'login_url' => 'http://localhost:8000/login',
        ]);

        Affiliation::create([
            'company_id' => $company->id,
            'affiliation_code' => 'PST',
            'affiliation_name' => 'Kantor Pusat',
            'display_order' => 1,
            'organization_type' => 1,
        ]);

        Affiliation::create([
            'company_id' => $company->id,
            'affiliation_code' => 'FC-001',
            'affiliation_name' => 'FC Cabang 1',
            'display_order' => 2,
            'organization_type' => 2,
        ]);

        MasterJob::create([
            'company_id' => $company->id,
            'job_id' => 'ADM',
            'job_title' => 'Administrator',
        ]);

        MasterJob::create([
            'company_id' => $company->id,
            'job_id' => 'MGR',
            'job_title' => 'Manager',
        ]);

        $admin = Employee::create([
            'company_id' => $company->id,
            'employee_code' => 'ADM001',
            'full_name' => 'Admin Demo',
            'email' => 'admin@demo.com',
            'password' => Hash::make('password123'),
            'account_status' => 'ACTIVE',
            'is_sys_admin' => true,
            'can_register_employee' => true,
            'can_register_course' => true,
            'can_setting_attendance' => true,
            'authority_effective_range' => 'ALL',
        ]);

        EmployeeAffiliation::create([
            'company_id' => $company->id,
            'employee_id' => $admin->id,
            'affiliation_code' => 'PST',
            'job_id' => 'ADM',
            'start_date' => now(),
        ]);

        $cat = CourseCategory::create([
            'category_code' => 'TECH',
            'category_name' => 'Teknis',
            'display_order' => 1,
        ]);

        $det = CourseCategoryDetail::create([
            'category_id' => $cat->id,
            'detail_code' => 'TCH-OPR',
            'detail_name' => 'Operasional Teknis',
            'display_order' => 1,
        ]);

        $course = Course::create([
            'category_detail_id' => $det->id,
            'course_name' => 'Pelatihan Awal Karyawan Baru',
            'passing_score' => 70,
        ]);

        CourseMaterial::create([
            'course_id' => $course->id,
            'material_type' => 'YOUTUBE',
            'title' => 'Video Pengenalan',
            'content_url_or_path' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'display_order' => 1,
        ]);

        CourseTodo::create([
            'course_id' => $course->id,
            'todo_type' => 'TEST',
            'title' => 'Ujian Pemahaman',
            'passing_score' => 70,
            'display_order' => 1,
        ]);

        CourseEnrollment::create([
            'course_id' => $course->id,
            'employee_id' => $admin->id,
            'enrollment_deadline' => now()->addDays(30),
            'status' => 'ENROLLED',
        ]);
    }
}
