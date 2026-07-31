<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Company;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseCategoryDetail;
use App\Models\CourseEnrollment;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
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

    public function test_profile_show_renders(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company, ['full_name' => 'Profile Viewer']);

        $this->actingAs($employee, 'employee')
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee('Profile Viewer');
    }

    public function test_profile_update_saves_fields(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);

        $this->actingAs($employee, 'employee')
            ->post(route('profile.update'), [
                'full_name' => 'Updated Name',
                'phone_number' => '081234567890',
                'gender' => 'MALE',
                'mfa_enabled' => true,
            ])
            ->assertRedirect(route('profile.show'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'full_name' => 'Updated Name',
            'phone_number' => '081234567890',
            'gender' => 'MALE',
            'mfa_enabled' => true,
        ]);
    }

    public function test_profile_update_rejects_missing_name(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);

        $this->actingAs($employee, 'employee')
            ->post(route('profile.update'), [
                'phone_number' => '081234567890',
            ])
            ->assertSessionHasErrors('full_name');
    }

    public function test_transcript_renders(): void
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
        Certificate::create([
            'enrollment_id' => $enrollment->id,
            'employee_id' => $employee->id,
            'course_id' => $course->id,
            'certificate_number' => 'OW-YOG-00001-20260731',
            'file_path' => 'certificates/test.pdf',
            'issued_at' => now(),
        ]);

        $this->actingAs($employee, 'employee')
            ->get(route('profile.transcript'))
            ->assertOk()
            ->assertSee($course->course_name);
    }
}
