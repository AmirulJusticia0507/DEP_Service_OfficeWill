<?php

namespace Tests\Feature;

use App\Models\Affiliation;
use App\Models\Company;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseCategoryDetail;
use App\Models\CourseEnrollment;
use App\Models\CourseTodo;
use App\Models\Employee;
use App\Models\MasterJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminMasterDataTest extends TestCase
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

    private function makeAffiliation(Company $company, string $code): Affiliation
    {
        return Affiliation::create([
            'company_id' => $company->id,
            'affiliation_code' => $code,
            'affiliation_name' => $code,
        ]);
    }

    private function makePosition(Company $company): MasterJob
    {
        return MasterJob::create([
            'company_id' => $company->id,
            'job_id' => 'JOB'.uniqid(),
            'job_title' => 'Job',
        ]);
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

    public function test_affiliation_crud_rejects_foreign_company(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company, ['is_sys_admin' => true]);

        $otherCompany = $this->makeCompany();
        $foreign = $this->makeAffiliation($otherCompany, 'FRG');

        $this->actingAs($operator, 'employee')
            ->get(route('admin.affiliations.edit', $foreign))
            ->assertNotFound();

        $this->actingAs($operator, 'employee')
            ->put(route('admin.affiliations.update', $foreign), [
                'affiliation_code' => 'FRG',
                'affiliation_name' => 'Tampered',
            ])
            ->assertNotFound();

        $this->actingAs($operator, 'employee')
            ->delete(route('admin.affiliations.destroy', $foreign))
            ->assertNotFound();

        $this->assertDatabaseHas('affiliations', ['id' => $foreign->id, 'affiliation_name' => 'FRG']);
    }

    public function test_affiliation_store_parent_must_be_same_company(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company, ['is_sys_admin' => true]);
        $this->makeAffiliation($company, 'OW');

        $otherCompany = $this->makeCompany();
        $this->makeAffiliation($otherCompany, 'FRG');

        $this->actingAs($operator, 'employee')
            ->post(route('admin.affiliations.store'), [
                'affiliation_code' => 'NEW',
                'affiliation_name' => 'New',
                'parent_affiliation_code' => 'FRG',
            ])
            ->assertSessionHasErrors('parent_affiliation_code');

        $this->assertDatabaseMissing('affiliations', ['affiliation_code' => 'NEW']);
    }

    public function test_position_crud_rejects_foreign_company(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company, ['is_sys_admin' => true]);

        $otherCompany = $this->makeCompany();
        $foreign = $this->makePosition($otherCompany);

        $this->actingAs($operator, 'employee')
            ->put(route('admin.positions.update', $foreign), [
                'job_id' => $foreign->job_id,
                'job_title' => 'Tampered',
            ])
            ->assertNotFound();

        $this->actingAs($operator, 'employee')
            ->delete(route('admin.positions.destroy', $foreign))
            ->assertNotFound();

        $this->assertDatabaseHas('master_jobs', ['id' => $foreign->id, 'job_title' => 'Job']);
    }

    public function test_authority_rejects_foreign_affiliation_code(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company, ['is_sys_admin' => true]);
        $target = $this->makeEmployee($company);

        $otherCompany = $this->makeCompany();
        $this->makeAffiliation($otherCompany, 'FRG');

        $this->actingAs($operator, 'employee')
            ->put(route('admin.authorities.update', $target), [
                'is_sys_admin' => false,
                'authority_effective_range' => 'ONLY',
                'authority_effective_affiliation_code' => 'FRG',
            ])
            ->assertSessionHasErrors('authority_effective_affiliation_code');
    }

    public function test_report_upload_accepts_pdf_and_rejects_other_types(): void
    {
        Storage::fake('local');

        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = CourseEnrollment::create([
            'course_id' => $course->id,
            'employee_id' => $employee->id,
            'enrollment_deadline' => now()->addWeek()->toDateString(),
            'status' => 'ENROLLED',
        ]);
        $todo = CourseTodo::create([
            'course_id' => $course->id,
            'todo_type' => 'REPORT',
            'title' => 'Report',
        ]);

        $this->actingAs($employee, 'employee')
            ->post(route('todos.report', $todo), [
                'report_file' => UploadedFile::fake()->create('report.txt', 100),
            ])
            ->assertSessionHasErrors('report_file');

        $this->assertDatabaseCount('course_todo_responses', 0);

        $this->actingAs($employee, 'employee')
            ->post(route('todos.report', $todo), [
                'report_file' => UploadedFile::fake()->create('report.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseCount('course_todo_responses', 1);
    }

    public function test_report_upload_rejects_oversized_file(): void
    {
        Storage::fake('local');

        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        CourseEnrollment::create([
            'course_id' => $course->id,
            'employee_id' => $employee->id,
            'enrollment_deadline' => now()->addWeek()->toDateString(),
            'status' => 'ENROLLED',
        ]);
        $todo = CourseTodo::create([
            'course_id' => $course->id,
            'todo_type' => 'REPORT',
            'title' => 'Report',
        ]);

        $this->actingAs($employee, 'employee')
            ->post(route('todos.report', $todo), [
                'report_file' => UploadedFile::fake()->create('report.pdf', 6000, 'application/pdf'),
            ])
            ->assertSessionHasErrors('report_file');

        $this->assertDatabaseCount('course_todo_responses', 0);
    }
}
