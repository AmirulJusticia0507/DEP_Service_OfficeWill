<?php

namespace Tests\Feature;

use App\Models\Affiliation;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeAffiliation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
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

    private function attachAffiliation(Company $company, Employee $employee, string $code): void
    {
        Affiliation::firstOrCreate([
            'company_id' => $company->id,
            'affiliation_code' => $code,
        ], ['affiliation_name' => $code]);

        EmployeeAffiliation::create([
            'company_id' => $company->id,
            'employee_id' => $employee->id,
            'affiliation_code' => $code,
            'start_date' => now()->toDateString(),
            'end_date' => null,
        ]);
    }

    public function test_api_employee_endpoint_is_closed(): void
    {
        $this->get('/api/employees')->assertNotFound();
        $this->get('/api')->assertNotFound();
    }

    public function test_non_sys_admin_cannot_access_authorities(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company, ['can_register_employee' => true]);

        $this->actingAs($employee, 'employee')
            ->get('/admin/authorities')
            ->assertForbidden();

        $this->actingAs($employee, 'employee')
            ->put('/admin/authorities/'.$employee->id, [
                'can_register_course' => true,
                'authority_effective_range' => 'ALL',
            ])
            ->assertForbidden();
    }

    public function test_sys_admin_can_access_authorities(): void
    {
        $company = $this->makeCompany();
        $admin = $this->makeEmployee($company, ['is_sys_admin' => true]);

        $this->actingAs($admin, 'employee')
            ->get('/admin/authorities')
            ->assertOk();
    }

    public function test_sys_admin_cannot_edit_own_authority(): void
    {
        $company = $this->makeCompany();
        $admin = $this->makeEmployee($company, ['is_sys_admin' => true]);

        $this->actingAs($admin, 'employee')
            ->put('/admin/authorities/'.$admin->id, [
                'authority_effective_range' => 'ALL',
            ])
            ->assertForbidden();
    }

    public function test_employee_without_permission_cannot_manage_employees(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);

        $this->actingAs($employee, 'employee')
            ->get('/employees')
            ->assertForbidden();

        $this->actingAs($employee, 'employee')
            ->get('/employees/create')
            ->assertForbidden();
    }

    public function test_employee_with_register_permission_can_manage_employees(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company, ['can_register_employee' => true]);

        $this->actingAs($employee, 'employee')
            ->get('/employees')
            ->assertOk();

        $this->actingAs($employee, 'employee')
            ->get('/employees/create')
            ->assertOk();
    }

    public function test_non_sys_admin_cannot_escalate_via_employee_update(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company, [
            'can_register_employee' => true,
            'authority_effective_range' => 'ONLY',
            'authority_effective_affiliation_code' => 'HRD',
        ]);
        $target = $this->makeEmployee($company);
        $this->attachAffiliation($company, $operator, 'HRD');
        $this->attachAffiliation($company, $target, 'HRD');

        $this->actingAs($operator, 'employee')
            ->put('/employees/'.$target->id, [
                'employee_code' => $target->employee_code,
                'full_name' => $target->full_name,
                'email' => $target->email,
                'can_register_course' => true,
                'is_sys_admin' => true,
                'authority_effective_range' => 'ALL',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/employees');

        $target->refresh();

        $this->assertFalse((bool) $target->can_register_course);
        $this->assertFalse((bool) $target->is_sys_admin);
        $this->assertSame('ONLY', $target->authority_effective_range);
    }

    public function test_sys_admin_can_grant_permissions(): void
    {
        $company = $this->makeCompany();
        $admin = $this->makeEmployee($company, ['is_sys_admin' => true]);
        $target = $this->makeEmployee($company);

        $this->actingAs($admin, 'employee')
            ->put('/employees/'.$target->id, [
                'employee_code' => $target->employee_code,
                'full_name' => $target->full_name,
                'email' => $target->email,
                'account_status' => 'ACTIVE',
                'authority_effective_range' => 'ALL',
                'can_register_course' => 1,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/employees');

        $target->refresh();

        $this->assertTrue((bool) $target->can_register_course);
        $this->assertSame('ALL', $target->authority_effective_range);
    }

    public function test_cross_company_employee_edit_is_forbidden(): void
    {
        $companyA = $this->makeCompany();
        $companyB = $this->makeCompany();
        $operator = $this->makeEmployee($companyA, ['can_register_employee' => true]);
        $other = $this->makeEmployee($companyB);

        $this->actingAs($operator, 'employee')
            ->get('/employees/'.$other->id.'/edit')
            ->assertForbidden();

        $this->actingAs($operator, 'employee')
            ->put('/employees/'.$other->id, [
                'employee_code' => $other->employee_code,
                'full_name' => $other->full_name,
                'email' => $other->email,
            ])
            ->assertForbidden();
    }

    public function test_sensitive_employee_fields_are_hidden(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company, ['mfa_otp_hash' => 'secret-hash']);

        $json = $employee->toArray();

        $this->assertArrayNotHasKey('password', $json);
        $this->assertArrayNotHasKey('mfa_otp_hash', $json);
        $this->assertArrayNotHasKey('mfa_otp_expires_at', $json);
    }
}
