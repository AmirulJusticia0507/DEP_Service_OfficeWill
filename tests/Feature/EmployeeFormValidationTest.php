<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmployeeFormValidationTest extends TestCase
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

    public function test_create_form_renders_validation_errors(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company, ['is_sys_admin' => true]);

        $this->actingAs($operator, 'employee')
            ->from(route('employees.create'))
            ->followingRedirects()
            ->post(route('employees.store'), [
                'full_name' => '',
                'email' => 'not-an-email',
            ])
            ->assertOk()
            ->assertSee('border-red-mark', false)
            ->assertSee('The email field must be a valid email address.');
    }

    public function test_edit_form_renders_validation_errors(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company, ['is_sys_admin' => true]);
        $target = $this->makeEmployee($company);

        $this->actingAs($operator, 'employee')
            ->from(route('employees.edit', $target))
            ->followingRedirects()
            ->put(route('employees.update', $target), [
                'employee_code' => $target->employee_code,
                'full_name' => '',
                'email' => $target->email,
            ])
            ->assertOk()
            ->assertSee('border-red-mark', false)
            ->assertSee('The full name field is required.');
    }
}
