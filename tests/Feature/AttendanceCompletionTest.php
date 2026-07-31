<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Company;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseCategoryDetail;
use App\Models\CourseEnrollment;
use App\Models\CourseTodo;
use App\Models\CourseTodoResponse;
use App\Models\Employee;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttendanceCompletionTest extends TestCase
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
            'course_name' => 'Safety Induction',
            'passing_score' => 70,
        ]);
    }

    private function makeEnrollment(Course $course, Employee $employee, array $overrides = []): CourseEnrollment
    {
        return CourseEnrollment::create(array_merge([
            'course_id' => $course->id,
            'employee_id' => $employee->id,
            'enrollment_deadline' => now()->addWeek()->toDateString(),
            'status' => 'ENROLLED',
        ], $overrides));
    }

    private function makePassedTodo(Course $course, CourseEnrollment $enrollment): void
    {
        $todo = CourseTodo::create([
            'course_id' => $course->id,
            'todo_type' => 'QUESTIONNAIRE',
            'title' => 'Questionnaire 1',
        ]);
        CourseTodoResponse::create([
            'enrollment_id' => $enrollment->id,
            'course_todo_id' => $todo->id,
            'response_content' => 'done',
            'status' => 'PASSED',
        ]);
    }

    public function test_employee_completes_course_with_single_attendance_and_certificate(): void
    {
        Storage::fake('public');

        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee);
        $this->makePassedTodo($course, $enrollment);

        $this->actingAs($employee, 'employee')
            ->post(route('attendance.complete', $enrollment))
            ->assertRedirect('/attendance')
            ->assertSessionHas('success');

        $this->assertDatabaseCount('attendances', 1);
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'enrollment_id' => $enrollment->id,
            'course_id' => $course->id,
            'status' => 'COMPLETED',
        ]);
        $this->assertDatabaseHas('course_enrollments', [
            'id' => $enrollment->id,
            'status' => 'COMPLETED',
        ]);
        $this->assertDatabaseCount('certificates', 1);
        $this->assertDatabaseCount('notifications', 1);
    }

    public function test_double_submit_does_not_create_duplicate_attendance_or_certificate(): void
    {
        Storage::fake('public');

        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee);
        $this->makePassedTodo($course, $enrollment);

        $this->actingAs($employee, 'employee')->post(route('attendance.complete', $enrollment));
        $this->actingAs($employee, 'employee')
            ->post(route('attendance.complete', $enrollment))
            ->assertRedirect('/attendance')
            ->assertSessionHas('info');

        $this->assertDatabaseCount('attendances', 1);
        $this->assertDatabaseCount('certificates', 1);
        $this->assertDatabaseCount('notifications', 1);
    }

    public function test_completion_is_blocked_after_deadline(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee, [
            'enrollment_deadline' => now()->subDay()->toDateString(),
        ]);

        $this->actingAs($employee, 'employee')
            ->post(route('attendance.complete', $enrollment))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseCount('attendances', 0);
        $this->assertDatabaseCount('certificates', 0);
        $this->assertDatabaseHas('course_enrollments', [
            'id' => $enrollment->id,
            'status' => 'ENROLLED',
        ]);
    }

    public function test_completion_is_blocked_when_todo_failed(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee);

        $todo = CourseTodo::create([
            'course_id' => $course->id,
            'todo_type' => 'TEST',
            'title' => 'Test 1',
        ]);
        CourseTodoResponse::create([
            'enrollment_id' => $enrollment->id,
            'course_todo_id' => $todo->id,
            'response_content' => 'wrong',
            'status' => 'FAILED',
        ]);

        $this->actingAs($employee, 'employee')
            ->post(route('attendance.complete', $enrollment))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseCount('attendances', 0);
        $this->assertDatabaseHas('course_enrollments', [
            'id' => $enrollment->id,
            'status' => 'ENROLLED',
        ]);
    }

    public function test_non_owner_cannot_complete_someone_elses_enrollment(): void
    {
        $company = $this->makeCompany();
        $owner = $this->makeEmployee($company);
        $intruder = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $owner);

        $this->actingAs($intruder, 'employee')
            ->post(route('attendance.complete', $enrollment))
            ->assertForbidden();

        $this->assertDatabaseCount('attendances', 0);
        $this->assertDatabaseHas('course_enrollments', [
            'id' => $enrollment->id,
            'status' => 'ENROLLED',
        ]);
    }

    public function test_attendance_unique_constraint_blocks_duplicate_attendance_for_same_enrollment(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee);

        Attendance::create([
            'employee_id' => $employee->id,
            'enrollment_id' => $enrollment->id,
            'course_id' => $course->id,
            'status' => 'COMPLETED',
            'attended_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        Attendance::create([
            'employee_id' => $employee->id,
            'enrollment_id' => $enrollment->id,
            'course_id' => $course->id,
            'status' => 'COMPLETED',
            'attended_at' => now(),
        ]);
    }
}
