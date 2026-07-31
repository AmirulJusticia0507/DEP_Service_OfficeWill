<?php

namespace Tests\Feature;

use App\Models\Affiliation;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeAffiliation;
use App\Models\MasterJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AssignmentPeriodTest extends TestCase
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
            'is_sys_admin' => true,
            'account_status' => 'ACTIVE',
            'authority_effective_range' => 'ALL',
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

    private function makeJob(Company $company, string $jobId = 'J1'): MasterJob
    {
        return MasterJob::create([
            'company_id' => $company->id,
            'job_id' => $jobId,
            'job_title' => 'Job '.$jobId,
        ]);
    }

    private function assign(Employee $employee, string $code, string $start, ?string $end, ?string $jobId = null): EmployeeAffiliation
    {
        return EmployeeAffiliation::create([
            'company_id' => $employee->company_id,
            'employee_id' => $employee->id,
            'affiliation_code' => $code,
            'job_id' => $jobId,
            'start_date' => $start,
            'end_date' => $end,
        ]);
    }

    public function test_edit_page_renders_assignments_section(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company);
        $target = $this->makeEmployee($company);
        $this->makeAffiliation($company, 'AFF-1');
        $this->assign($target, 'AFF-1', '2026-01-01', null);

        $this->actingAs($operator, 'employee')
            ->get(route('employees.edit', $target))
            ->assertOk()
            ->assertSee('Masa Jabatan');
    }

    public function test_operator_can_create_assignment(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company);
        $target = $this->makeEmployee($company);
        $this->makeAffiliation($company, 'AFF-1');

        $this->actingAs($operator, 'employee')
            ->post(route('employees.assignments.store', $target), [
                'affiliation_code' => 'AFF-1',
                'job_id' => null,
                'start_date' => '2026-01-01',
                'end_date' => '2026-06-30',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('employee_affiliations', [
            'employee_id' => $target->id,
            'affiliation_code' => 'AFF-1',
            'start_date' => '2026-01-01 00:00:00',
            'end_date' => '2026-06-30 00:00:00',
        ]);
    }

    public function test_overlapping_affiliation_period_is_rejected(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company);
        $target = $this->makeEmployee($company);
        $this->makeAffiliation($company, 'AFF-1');
        $this->assign($target, 'AFF-1', '2026-01-01', '2026-06-30');

        $this->actingAs($operator, 'employee')
            ->post(route('employees.assignments.store', $target), [
                'affiliation_code' => 'AFF-1',
                'start_date' => '2026-03-01',
                'end_date' => '2026-04-30',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('employee_affiliations', 1);
    }

    public function test_open_ended_affiliation_overlaps_any_new_period(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company);
        $target = $this->makeEmployee($company);
        $this->makeAffiliation($company, 'AFF-1');
        $this->assign($target, 'AFF-1', '2026-01-01', null);

        $this->actingAs($operator, 'employee')
            ->post(route('employees.assignments.store', $target), [
                'affiliation_code' => 'AFF-1',
                'start_date' => '2026-07-01',
                'end_date' => '2026-12-31',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('employee_affiliations', 1);
    }

    public function test_same_job_overlap_is_rejected_even_for_different_affiliation(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company);
        $target = $this->makeEmployee($company);
        $this->makeAffiliation($company, 'AFF-1');
        $this->makeAffiliation($company, 'AFF-2');
        $this->makeJob($company, 'J1');
        $this->assign($target, 'AFF-1', '2026-01-01', '2026-06-30', 'J1');

        $this->actingAs($operator, 'employee')
            ->post(route('employees.assignments.store', $target), [
                'affiliation_code' => 'AFF-2',
                'job_id' => 'J1',
                'start_date' => '2026-03-01',
                'end_date' => null,
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('employee_affiliations', 1);
    }

    public function test_sequential_periods_are_allowed(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company);
        $target = $this->makeEmployee($company);
        $this->makeAffiliation($company, 'AFF-1');
        $this->assign($target, 'AFF-1', '2026-01-01', '2026-06-30');

        $this->actingAs($operator, 'employee')
            ->post(route('employees.assignments.store', $target), [
                'affiliation_code' => 'AFF-1',
                'start_date' => '2026-07-01',
                'end_date' => '2026-12-31',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseCount('employee_affiliations', 2);
    }

    public function test_new_open_ended_after_closed_period_is_allowed(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company);
        $target = $this->makeEmployee($company);
        $this->makeAffiliation($company, 'AFF-1');
        $this->assign($target, 'AFF-1', '2026-01-01', '2026-06-30');

        $this->actingAs($operator, 'employee')
            ->post(route('employees.assignments.store', $target), [
                'affiliation_code' => 'AFF-1',
                'start_date' => '2026-07-01',
                'end_date' => null,
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseCount('employee_affiliations', 2);
    }

    public function test_end_date_before_start_date_is_rejected(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company);
        $target = $this->makeEmployee($company);
        $this->makeAffiliation($company, 'AFF-1');

        $this->actingAs($operator, 'employee')
            ->post(route('employees.assignments.store', $target), [
                'affiliation_code' => 'AFF-1',
                'start_date' => '2026-06-01',
                'end_date' => '2026-01-01',
            ])
            ->assertSessionHasErrors('end_date');

        $this->assertDatabaseCount('employee_affiliations', 0);
    }

    public function test_operator_outside_scope_cannot_assign_out_of_scope_affiliation(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company, [
            'is_sys_admin' => false,
            'can_register_employee' => true,
            'authority_effective_range' => 'ONLY',
            'authority_effective_affiliation_code' => 'AFF-1',
        ]);
        $target = $this->makeEmployee($company);
        $this->makeAffiliation($company, 'AFF-1');
        $this->makeAffiliation($company, 'AFF-2');
        $this->assign($target, 'AFF-1', '2026-01-01', null);

        $this->actingAs($operator, 'employee')
            ->post(route('employees.assignments.store', $target), [
                'affiliation_code' => 'AFF-2',
                'start_date' => '2026-03-01',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('employee_affiliations', 1);
    }

    public function test_cross_company_affiliation_is_rejected(): void
    {
        $companyA = $this->makeCompany();
        $companyB = $this->makeCompany();
        $operator = $this->makeEmployee($companyA);
        $target = $this->makeEmployee($companyA);
        $this->makeAffiliation($companyB, 'AFF-OTHER');

        $this->actingAs($operator, 'employee')
            ->post(route('employees.assignments.store', $target), [
                'affiliation_code' => 'AFF-OTHER',
                'start_date' => '2026-01-01',
            ])
            ->assertSessionHasErrors('affiliation_code');

        $this->assertDatabaseCount('employee_affiliations', 0);
    }

    public function test_assignment_can_be_ended_only_once(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company);
        $target = $this->makeEmployee($company);
        $this->makeAffiliation($company, 'AFF-1');
        $assignment = $this->assign($target, 'AFF-1', '2026-01-01', null);

        $this->actingAs($operator, 'employee')
            ->put(route('employees.assignments.end', [$target, $assignment]))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('employee_affiliations', [
            'id' => $assignment->id,
            'end_date' => now()->format('Y-m-d 00:00:00'),
        ]);

        $this->actingAs($operator, 'employee')
            ->put(route('employees.assignments.end', [$target, $assignment]))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('employee_affiliations', 1);
    }

    public function test_assignment_can_be_deleted(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company);
        $target = $this->makeEmployee($company);
        $this->makeAffiliation($company, 'AFF-1');
        $assignment = $this->assign($target, 'AFF-1', '2026-01-01', null);

        $this->actingAs($operator, 'employee')
            ->delete(route('employees.assignments.destroy', [$target, $assignment]))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('employee_affiliations', 0);
    }
}
