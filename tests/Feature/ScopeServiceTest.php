<?php

namespace Tests\Feature;

use App\Models\Affiliation;
use App\Models\Company;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseCategoryDetail;
use App\Models\CourseEnrollment;
use App\Models\Employee;
use App\Models\EmployeeAffiliation;
use App\Services\ScopeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ScopeServiceTest extends TestCase
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

    private function assignAffiliation(Employee $employee, string $code): EmployeeAffiliation
    {
        return EmployeeAffiliation::create([
            'company_id' => $employee->company_id,
            'employee_id' => $employee->id,
            'affiliation_code' => $code,
            'start_date' => now()->toDateString(),
            'end_date' => null,
        ]);
    }

    private function makeCourse(string $name = 'Test Course'): Course
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
            'course_name' => $name,
            'passing_score' => 70,
        ]);
    }

    private function enroll(Company $company, Employee $employee, Course $course): CourseEnrollment
    {
        return CourseEnrollment::create([
            'course_id' => $course->id,
            'employee_id' => $employee->id,
            'enrollment_deadline' => now()->addDays(7)->toDateString(),
            'status' => 'ENROLLED',
        ]);
    }

    public function test_only_scope_can_access_same_affiliation_only(): void
    {
        $company = $this->makeCompany();
        $this->makeAffiliation($company, 'HRD');
        $this->makeAffiliation($company, 'IT');

        $operator = $this->makeEmployee($company, [
            'can_register_employee' => true,
            'authority_effective_range' => 'ONLY',
            'authority_effective_affiliation_code' => 'HRD',
        ]);
        $this->assignAffiliation($operator, 'HRD');

        $hrdTarget = $this->makeEmployee($company);
        $this->assignAffiliation($hrdTarget, 'HRD');

        $itTarget = $this->makeEmployee($company);
        $this->assignAffiliation($itTarget, 'IT');

        $otherCompany = $this->makeCompany();
        $this->makeAffiliation($otherCompany, 'HRD');
        $foreign = $this->makeEmployee($otherCompany);
        $this->assignAffiliation($foreign, 'HRD');

        $scope = app(ScopeService::class);

        $this->assertTrue($scope->canAccessEmployee($operator, $hrdTarget));
        $this->assertFalse($scope->canAccessEmployee($operator, $itTarget));
        $this->assertFalse($scope->canAccessEmployee($operator, $foreign));
    }

    public function test_below_scope_includes_descendants(): void
    {
        $company = $this->makeCompany();
        $this->makeAffiliation($company, 'OW');
        $this->makeAffiliation($company, 'OW-HRD');
        $this->makeAffiliation($company, 'OW-IT');

        $operator = $this->makeEmployee($company, [
            'authority_effective_range' => 'BELOW',
            'authority_effective_affiliation_code' => 'OW',
        ]);
        $this->assignAffiliation($operator, 'OW');

        $a = $this->makeEmployee($company);
        $this->assignAffiliation($a, 'OW');
        $b = $this->makeEmployee($company);
        $this->assignAffiliation($b, 'OW-HRD');
        $c = $this->makeEmployee($company);
        $this->assignAffiliation($c, 'OW-IT');
        $d = $this->makeEmployee($company);
        $this->assignAffiliation($d, 'XYZ');

        $scope = app(ScopeService::class);

        $this->assertTrue($scope->canAccessEmployee($operator, $a));
        $this->assertTrue($scope->canAccessEmployee($operator, $b));
        $this->assertTrue($scope->canAccessEmployee($operator, $c));
        $this->assertFalse($scope->canAccessEmployee($operator, $d));
    }

    public function test_all_scope_accesses_all_in_company(): void
    {
        $company = $this->makeCompany();
        $this->makeAffiliation($company, 'HRD');

        $operator = $this->makeEmployee($company, [
            'authority_effective_range' => 'ALL',
        ]);

        $target = $this->makeEmployee($company);
        $this->assignAffiliation($target, 'HRD');

        $foreign = $this->makeEmployee($this->makeCompany());
        $this->assignAffiliation($foreign, 'HRD');

        $scope = app(ScopeService::class);

        $this->assertTrue($scope->canAccessEmployee($operator, $target));
        $this->assertFalse($scope->canAccessEmployee($operator, $foreign));
    }

    public function test_fail_closed_without_affiliation_code(): void
    {
        $company = $this->makeCompany();
        $this->makeAffiliation($company, 'HRD');

        $operator = $this->makeEmployee($company, [
            'authority_effective_range' => 'ONLY',
            'authority_effective_affiliation_code' => null,
        ]);

        $target = $this->makeEmployee($company);
        $this->assignAffiliation($target, 'HRD');

        $scope = app(ScopeService::class);

        $this->assertFalse($scope->canAccessEmployee($operator, $target));

        $ids = $scope->scopeEmployeeQuery(Employee::where('company_id', $company->id), $operator)->pluck('id');
        $this->assertEmpty($ids);
    }

    public function test_scope_employee_query_filters_by_affiliation(): void
    {
        $company = $this->makeCompany();
        $this->makeAffiliation($company, 'HRD');
        $this->makeAffiliation($company, 'IT');

        $operator = $this->makeEmployee($company, [
            'authority_effective_range' => 'ONLY',
            'authority_effective_affiliation_code' => 'HRD',
        ]);
        $this->assignAffiliation($operator, 'HRD');

        $hrd = $this->makeEmployee($company);
        $this->assignAffiliation($hrd, 'HRD');
        $it = $this->makeEmployee($company);
        $this->assignAffiliation($it, 'IT');

        $ids = app(ScopeService::class)
            ->scopeEmployeeQuery(Employee::where('company_id', $company->id), $operator)
            ->pluck('id');

        $this->assertContains($hrd->id, $ids);
        $this->assertNotContains($it->id, $ids);
    }

    public function test_scope_enrollment_query_filters_by_affiliation(): void
    {
        $company = $this->makeCompany();
        $this->makeAffiliation($company, 'HRD');
        $this->makeAffiliation($company, 'IT');
        $course = $this->makeCourse('Course A');

        $operator = $this->makeEmployee($company, [
            'authority_effective_range' => 'ONLY',
            'authority_effective_affiliation_code' => 'HRD',
        ]);
        $this->assignAffiliation($operator, 'HRD');

        $hrd = $this->makeEmployee($company);
        $this->assignAffiliation($hrd, 'HRD');
        $it = $this->makeEmployee($company);
        $this->assignAffiliation($it, 'IT');

        $inScope = $this->enroll($company, $hrd, $course);
        $outOfScope = $this->enroll($company, $it, $this->makeCourse('Course B'));

        $ids = app(ScopeService::class)
            ->scopeEnrollmentQuery(CourseEnrollment::query(), $operator)
            ->pluck('id');

        $this->assertContains($inScope->id, $ids);
        $this->assertNotContains($outOfScope->id, $ids);
    }

    public function test_assignment_cannot_enroll_out_of_scope(): void
    {
        $company = $this->makeCompany();
        $this->makeAffiliation($company, 'HRD');
        $this->makeAffiliation($company, 'IT');
        $course = $this->makeCourse();

        $operator = $this->makeEmployee($company, [
            'can_register_course' => true,
            'authority_effective_range' => 'ONLY',
            'authority_effective_affiliation_code' => 'HRD',
        ]);
        $this->assignAffiliation($operator, 'HRD');

        $hrd = $this->makeEmployee($company);
        $this->assignAffiliation($hrd, 'HRD');
        $it = $this->makeEmployee($company);
        $this->assignAffiliation($it, 'IT');

        $foreign = $this->makeEmployee($this->makeCompany());
        $this->assignAffiliation($foreign, 'HRD');

        $payload = [
            'course_id' => $course->id,
            'enrollment_deadline' => now()->addDays(7)->toDateString(),
        ];

        $this->actingAs($operator, 'employee')
            ->post('/admin/assignments', $payload + ['employee_ids' => [$it->id]])
            ->assertForbidden();

        $this->actingAs($operator, 'employee')
            ->post('/admin/assignments', $payload + ['employee_ids' => [$foreign->id]])
            ->assertForbidden();

        $this->actingAs($operator, 'employee')
            ->post('/admin/assignments', $payload + ['employee_ids' => [$hrd->id]])
            ->assertRedirect('/admin/assignments');
    }

    public function test_enrollment_ops_are_scoped(): void
    {
        $company = $this->makeCompany();
        $this->makeAffiliation($company, 'HRD');
        $this->makeAffiliation($company, 'IT');
        $inCourse = $this->makeCourse('Course In Scope');
        $outCourse = $this->makeCourse('Course Out Of Scope');

        $operator = $this->makeEmployee($company, [
            'can_register_course' => true,
            'authority_effective_range' => 'ONLY',
            'authority_effective_affiliation_code' => 'HRD',
        ]);
        $this->assignAffiliation($operator, 'HRD');

        $hrd = $this->makeEmployee($company);
        $this->assignAffiliation($hrd, 'HRD');
        $it = $this->makeEmployee($company);
        $this->assignAffiliation($it, 'IT');

        $inScope = $this->enroll($company, $hrd, $inCourse);
        $outOfScope = $this->enroll($company, $it, $outCourse);

        $this->actingAs($operator, 'employee')
            ->put('/enrollments/'.$outOfScope->id, ['status' => 'CANCELLED'])
            ->assertForbidden();

        $this->actingAs($operator, 'employee')
            ->put('/enrollments/'.$inScope->id, ['status' => 'CANCELLED'])
            ->assertRedirect();

        $this->actingAs($operator, 'employee')
            ->get('/enrollments')
            ->assertOk()
            ->assertSee($inScope->course->course_name, false)
            ->assertDontSee($outOfScope->course->course_name, false);
    }
}
