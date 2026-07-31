<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Company;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseCategoryDetail;
use App\Models\CourseEnrollment;
use App\Models\CourseTodo;
use App\Models\Employee;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateSecurityTest extends TestCase
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
        ]);
    }

    private function makeEnrollment(Course $course, Employee $employee, array $overrides = []): CourseEnrollment
    {
        return CourseEnrollment::create(array_merge([
            'course_id' => $course->id,
            'employee_id' => $employee->id,
            'enrollment_deadline' => now()->addWeek()->toDateString(),
            'status' => 'COMPLETED',
        ], $overrides));
    }

    public function test_generate_creates_certificate(): void
    {
        Storage::fake('public');

        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee);

        $this->actingAs($employee, 'employee')
            ->post(route('certificates.generate', $enrollment))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('certificates', 1);
        $certificate = Certificate::first();
        $this->assertTrue(Storage::disk('public')->exists($certificate->file_path));
    }

    public function test_double_generate_is_idempotent(): void
    {
        Storage::fake('public');

        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee);

        $this->actingAs($employee, 'employee')
            ->post(route('certificates.generate', $enrollment))
            ->assertSessionHas('success');
        $this->actingAs($employee, 'employee')
            ->post(route('certificates.generate', $enrollment))
            ->assertSessionHas('info');

        $this->assertDatabaseCount('certificates', 1);
    }

    public function test_generate_blocked_when_course_not_completed(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee, ['status' => 'ENROLLED']);

        $this->actingAs($employee, 'employee')
            ->post(route('certificates.generate', $enrollment))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_generate_requires_owner(): void
    {
        $company = $this->makeCompany();
        $owner = $this->makeEmployee($company);
        $intruder = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $owner);

        $this->actingAs($intruder, 'employee')
            ->post(route('certificates.generate', $enrollment))
            ->assertForbidden();

        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_download_requires_owner(): void
    {
        $company = $this->makeCompany();
        $owner = $this->makeEmployee($company);
        $intruder = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $owner);

        $certificate = Certificate::create([
            'enrollment_id' => $enrollment->id,
            'employee_id' => $owner->id,
            'course_id' => $course->id,
            'certificate_number' => 'OW-TEST-00001',
            'file_path' => 'certificates/ow-test.pdf',
            'issued_at' => now(),
        ]);

        $this->actingAs($intruder, 'employee')
            ->get(route('certificates.download', $certificate))
            ->assertForbidden();
    }

    public function test_grade_rejects_answers_from_another_attempt(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company);
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee);
        $todo = CourseTodo::create([
            'course_id' => $course->id,
            'todo_type' => 'TEST',
            'title' => 'Test 1',
        ]);
        $question = Question::create([
            'course_id' => $course->id,
            'question_type' => 'ESSAY',
            'question_text' => 'Essay?',
            'points' => 10,
        ]);

        $attemptOne = ExamAttempt::create([
            'enrollment_id' => $enrollment->id,
            'course_todo_id' => $todo->id,
            'attempt_number' => 1,
            'status' => 'IN_PROGRESS',
            'started_at' => now(),
            'max_score' => 10,
        ]);
        $attemptTwo = ExamAttempt::create([
            'enrollment_id' => $enrollment->id,
            'course_todo_id' => $todo->id,
            'attempt_number' => 2,
            'status' => 'IN_PROGRESS',
            'started_at' => now(),
            'max_score' => 10,
        ]);

        $answerOne = ExamAnswer::create([
            'exam_attempt_id' => $attemptOne->id,
            'question_id' => $question->id,
            'essay_answer' => 'jawaban 1',
            'points_earned' => 0,
        ]);
        ExamAnswer::create([
            'exam_attempt_id' => $attemptTwo->id,
            'question_id' => $question->id,
            'essay_answer' => 'jawaban 2',
            'points_earned' => 0,
        ]);

        $this->actingAs($operator, 'employee')
            ->post(route('exam.grade', $attemptOne), [
                'scores' => [$answerOne->id => 10],
            ])
            ->assertSessionHas('success');

        $this->actingAs($operator, 'employee')
            ->post(route('exam.grade', $attemptOne), [
                'scores' => [$answerOne->id + 1 => 10],
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('exam_answers', [
            'id' => $answerOne->id,
            'points_earned' => 10,
        ]);
    }
}
