<?php

namespace Tests\Feature;

use App\Helpers\NotificationHelper;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
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

    public function test_index_returns_own_notifications(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $other = $this->makeEmployee($company);

        NotificationHelper::send($employee, 'course_assigned', 'Assigned', 'Yours');
        NotificationHelper::send($other, 'course_assigned', 'Assigned', 'Theirs');

        $response = $this->actingAs($employee, 'employee')
            ->get(route('notifications.index'))
            ->assertOk();

        $this->assertStringContainsString('Yours', $response->getContent());
        $this->assertStringNotContainsString('Theirs', $response->getContent());
    }

    public function test_mark_read_own_notification(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $notification = NotificationHelper::send($employee, 'course_assigned', 'Assigned', 'Yours');

        $this->actingAs($employee, 'employee')
            ->post(route('notifications.read', $notification))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'is_read' => true,
        ]);
    }

    public function test_mark_read_other_employees_notification_forbidden(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $other = $this->makeEmployee($company);
        $notification = NotificationHelper::send($other, 'course_assigned', 'Assigned', 'Theirs');

        $this->actingAs($employee, 'employee')
            ->post(route('notifications.read', $notification))
            ->assertForbidden();

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'is_read' => false,
        ]);
    }

    public function test_mark_all_read_only_affects_own(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $other = $this->makeEmployee($company);

        $ownOne = NotificationHelper::send($employee, 'course_assigned', 'Assigned', 'Yours 1');
        $ownTwo = NotificationHelper::send($employee, 'course_assigned', 'Assigned', 'Yours 2');
        $theirs = NotificationHelper::send($other, 'course_assigned', 'Assigned', 'Theirs');

        $this->actingAs($employee, 'employee')
            ->post(route('notifications.read-all'))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('notifications', ['id' => $ownOne->id, 'is_read' => true]);
        $this->assertDatabaseHas('notifications', ['id' => $ownTwo->id, 'is_read' => true]);
        $this->assertDatabaseHas('notifications', ['id' => $theirs->id, 'is_read' => false]);
    }

    public function test_unread_count(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);

        NotificationHelper::send($employee, 'course_assigned', 'Assigned', 'Yours 1');
        NotificationHelper::send($employee, 'course_assigned', 'Assigned', 'Yours 2');

        $this->actingAs($employee, 'employee')
            ->get(route('notifications.unread-count'))
            ->assertOk()
            ->assertJson(['count' => 2]);
    }
}
