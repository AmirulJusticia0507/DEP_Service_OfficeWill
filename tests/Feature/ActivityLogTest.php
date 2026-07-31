<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseCategoryDetail;
use App\Models\CourseEnrollment;
use App\Models\Employee;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ActivityLogTest extends TestCase
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

    private function makeCourse(): Course
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

        return Course::create([
            'category_detail_id' => $detail->id,
            'course_name' => 'Course '.uniqid(),
        ]);
    }

    public function test_login_success_is_logged(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company, ['email' => 'login@test.local']);

        $this->post(route('login'), ['email' => 'login@test.local', 'password' => 'secret'])
            ->assertRedirect('/dashboard');

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'login',
            'employee_id' => $employee->id,
            'company_id' => $company->id,
        ]);
    }

    public function test_login_failure_is_logged(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company, ['email' => 'login@test.local']);

        $this->post(route('login'), ['email' => 'login@test.local', 'password' => 'wrong-password'])
            ->assertSessionHasErrors('email');

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'login_failed',
            'subject_id' => $employee->id,
        ]);
    }

    public function test_password_change_is_logged(): void
    {
        Mail::fake();

        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);

        $this->actingAs($employee, 'employee')
            ->post(route('change-password'), [
                'current_password' => 'secret',
                'new_password' => 'newpassword123',
                'new_password_confirmation' => 'newpassword123',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'password_change',
            'employee_id' => $employee->id,
        ]);
    }

    public function test_employee_registration_is_logged(): void
    {
        Mail::fake();

        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company, ['is_sys_admin' => true]);

        $this->actingAs($operator, 'employee')
            ->post(route('employees.store'), [
                'employee_code' => 'NEWEMP',
                'full_name' => 'New Employee',
                'email' => 'newemp@test.local',
                'authority_effective_range' => 'ALL',
            ])
            ->assertRedirect('/employees');

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'employee_create',
            'company_id' => $company->id,
        ]);
    }

    public function test_authority_update_is_logged(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company, ['is_sys_admin' => true]);
        $target = $this->makeEmployee($company);

        $this->actingAs($operator, 'employee')
            ->put(route('admin.authorities.update', $target), [
                'is_sys_admin' => false,
                'can_register_course' => true,
                'authority_effective_range' => 'ALL',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'authority_update',
            'employee_id' => $operator->id,
            'subject_id' => $target->id,
        ]);
    }

    public function test_certificate_generate_is_logged(): void
    {
        Storage::fake('public');

        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = CourseEnrollment::create([
            'course_id' => $course->id,
            'employee_id' => $employee->id,
            'enrollment_deadline' => now()->addWeek()->toDateString(),
            'status' => 'COMPLETED',
        ]);

        $this->actingAs($employee, 'employee')
            ->post(route('certificates.generate', $enrollment))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'certificate_generate',
            'employee_id' => $employee->id,
        ]);
    }

    public function test_log_viewer_scoped_to_company(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company, ['is_sys_admin' => true, 'full_name' => 'Own Admin']);
        $otherCompany = $this->makeCompany();
        $foreign = $this->makeEmployee($otherCompany);

        $this->actingAs($operator, 'employee');
        ActivityLogger::log('login', 'Own company login event.', $operator);

        $this->actingAs($foreign, 'employee');
        ActivityLogger::log('login', 'Foreign company login event.', $foreign);

        $this->actingAs($operator, 'employee')
            ->get(route('admin.logs.index'))
            ->assertOk()
            ->assertSee('Own company login event.')
            ->assertDontSee('Foreign company login event.');
    }

    public function test_log_viewer_forbidden_for_non_admin(): void
    {
        $company = $this->makeCompany();
        $plain = $this->makeEmployee($company);

        $this->actingAs($plain, 'employee')
            ->get(route('admin.logs.index'))
            ->assertForbidden();
    }
}
