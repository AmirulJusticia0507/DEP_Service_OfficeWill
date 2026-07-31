<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseCategoryDetail;
use App\Models\CourseEnrollment;
use App\Models\CourseTodo;
use App\Models\Employee;
use App\Models\ExamAttempt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PermissionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private function makeCompany(): Company
    {
        return Company::create([
            'company_name' => 'Test Corp',
            'login_url' => 'https://test.local',
        ]);
    }

    private function makeEmployee(Company $company, array $overrides = []): Employee
    {
        return Employee::create(array_merge([
            'company_id' => $company->id,
            'employee_code' => 'EMP'.uniqid(),
            'full_name' => 'Test User',
            'email' => uniqid().'@test.local',
            'password' => Hash::make('secret'),
            'is_sys_admin' => false,
            'account_status' => 'ACTIVE',
        ], $overrides));
    }

    public function test_courses_require_can_register_course(): void
    {
        $company = $this->makeCompany();
        $plain = $this->makeEmployee($company);
        $registrar = $this->makeEmployee($company, ['can_register_course' => true]);

        $this->actingAs($plain, 'employee')->get('/courses')->assertForbidden();
        $this->actingAs($registrar, 'employee')->get('/courses')->assertOk();
    }

    public function test_assignments_require_can_register_course(): void
    {
        $company = $this->makeCompany();
        $plain = $this->makeEmployee($company);
        $registrar = $this->makeEmployee($company, ['can_register_course' => true]);

        $this->actingAs($plain, 'employee')->get('/admin/assignments')->assertForbidden();
        $this->actingAs($registrar, 'employee')->get('/admin/assignments')->assertOk();
    }

    public function test_master_data_is_sys_admin_only(): void
    {
        $company = $this->makeCompany();
        $registrar = $this->makeEmployee($company, ['can_register_course' => true, 'can_register_employee' => true]);
        $admin = $this->makeEmployee($company, ['is_sys_admin' => true]);

        $this->actingAs($registrar, 'employee')->get('/admin/affiliations')->assertForbidden();
        $this->actingAs($registrar, 'employee')->get('/admin/positions')->assertForbidden();
        $this->actingAs($registrar, 'employee')->get('/admin/course-categories')->assertForbidden();
        $this->actingAs($registrar, 'employee')->get('/admin/mail-log')->assertForbidden();

        $this->actingAs($admin, 'employee')->get('/admin/affiliations')->assertOk();
        $this->actingAs($admin, 'employee')->get('/admin/mail-log')->assertOk();
    }

    public function test_exam_grade_route_is_gated(): void
    {
        $company = $this->makeCompany();
        $plain = $this->makeEmployee($company);
        $registrar = $this->makeEmployee($company, ['can_register_course' => true]);

        $attempt = $this->makeAttempt($company, $registrar);

        $this->actingAs($plain, 'employee')
            ->post('/exam/grade/'.$attempt->id, ['scores' => []])
            ->assertForbidden();

        $this->actingAs($registrar, 'employee')
            ->post('/exam/grade/'.$attempt->id, ['scores' => []])
            ->assertRedirect();
    }

    private function makeAttempt(Company $company, Employee $employee)
    {
        $category = CourseCategory::create([
            'category_code' => 'CAT'.uniqid(),
            'category_name' => 'Category',
        ]);
        $detail = CourseCategoryDetail::create([
            'category_id' => $category->id,
            'detail_code' => 'DET'.uniqid(),
            'detail_name' => 'Detail',
        ]);
        $course = Course::create([
            'category_detail_id' => $detail->id,
            'course_name' => 'Test Course',
            'passing_score' => 70,
        ]);
        $enrollment = CourseEnrollment::create([
            'course_id' => $course->id,
            'employee_id' => $employee->id,
            'enrollment_deadline' => now()->addDays(30)->toDateString(),
        ]);
        $todo = CourseTodo::create([
            'course_id' => $course->id,
            'todo_type' => 'TEST',
            'title' => 'Final Test',
        ]);

        return ExamAttempt::create([
            'enrollment_id' => $enrollment->id,
            'course_todo_id' => $todo->id,
            'status' => 'IN_PROGRESS',
        ]);
    }

    public function test_has_permission_helper(): void
    {
        $company = $this->makeCompany();
        $plain = $this->makeEmployee($company);
        $registrar = $this->makeEmployee($company, ['can_register_course' => true]);
        $admin = $this->makeEmployee($company, ['is_sys_admin' => true]);

        $this->assertFalse($plain->hasPermission('can_register_course'));
        $this->assertTrue($registrar->hasPermission('can_register_course'));
        $this->assertTrue($registrar->hasPermission('can_register_employee', 'can_register_course'));
        $this->assertFalse($registrar->hasPermission('can_register_employee', 'can_setting_attendance'));
        $this->assertTrue($admin->hasPermission('can_register_course'));
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($plain->isAdmin());
    }
}
