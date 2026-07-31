<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthRateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    private function makeCompany(): Company
    {
        return Company::create([
            'company_name' => 'Test Corp',
            'login_url' => 'https://test.local',
        ]);
    }

    public function test_login_is_rate_limited(): void
    {
        $company = $this->makeCompany();
        Employee::create([
            'company_id' => $company->id,
            'employee_code' => 'EMP1',
            'full_name' => 'Test User',
            'email' => 'test@test.local',
            'password' => Hash::make('secret'),
            'is_sys_admin' => false,
            'account_status' => 'ACTIVE',
        ]);

        for ($i = 0; $i < 10; $i++) {
            $response = $this->post('/login', [
                'email' => 'test@test.local',
                'password' => 'wrong-password',
            ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        $this->post('/login', [
            'email' => 'test@test.local',
            'password' => 'wrong-password',
        ])->assertStatus(429);
    }

    public function test_reissue_password_is_rate_limited(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $response = $this->post('/reissue-password', ['email' => 'none@test.local']);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        $this->post('/reissue-password', ['email' => 'none@test.local'])
            ->assertStatus(429);
    }
}
