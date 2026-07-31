<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseCategoryDetail;
use App\Models\CourseEnrollment;
use App\Models\CourseTodo;
use App\Models\CourseTodoResponse;
use App\Models\Employee;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TodoSubmissionTest extends TestCase
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

    private function makeTodo(Course $course, string $type = 'QUESTIONNAIRE', array $overrides = []): CourseTodo
    {
        return CourseTodo::create(array_merge([
            'course_id' => $course->id,
            'todo_type' => $type,
            'title' => 'Todo '.uniqid(),
        ], $overrides));
    }

    private function makeQuestion(Course $course, array $overrides = []): Question
    {
        return Question::create(array_merge([
            'course_id' => $course->id,
            'question_type' => 'MCQ',
            'question_text' => 'Question?',
            'points' => 10,
        ], $overrides));
    }

    public function test_questionnaire_double_submit_keeps_single_response(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee);
        $todo = $this->makeTodo($course);

        $this->actingAs($employee, 'employee')
            ->post(route('todos.questionnaire', $todo), ['response_content' => 'pertama'])
            ->assertSessionHas('success');
        $this->actingAs($employee, 'employee')
            ->post(route('todos.questionnaire', $todo), ['response_content' => 'kedua'])
            ->assertSessionHas('success');

        $this->assertDatabaseCount('course_todo_responses', 1);
        $this->assertDatabaseHas('course_todo_responses', [
            'enrollment_id' => $enrollment->id,
            'course_todo_id' => $todo->id,
            'response_content' => 'kedua',
            'status' => 'PASSED',
        ]);
    }

    public function test_report_reupload_replaces_file_and_keeps_single_response(): void
    {
        Storage::fake('local');

        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee);
        $todo = $this->makeTodo($course, 'REPORT');

        $first = UploadedFile::fake()->create('first.pdf', 100, 'application/pdf');
        $this->actingAs($employee, 'employee')
            ->post(route('todos.report', $todo), ['report_file' => $first])
            ->assertSessionHas('success');

        $firstPath = CourseTodoResponse::first()->response_content;
        $this->assertTrue(Storage::exists($firstPath));

        $second = UploadedFile::fake()->create('second.pdf', 100, 'application/pdf');
        $this->actingAs($employee, 'employee')
            ->post(route('todos.report', $todo), ['report_file' => $second])
            ->assertSessionHas('success');

        $this->assertDatabaseCount('course_todo_responses', 1);
        $secondPath = CourseTodoResponse::first()->response_content;
        $this->assertNotSame($firstPath, $secondPath);
        $this->assertTrue(Storage::exists($secondPath));
        $this->assertFalse(Storage::exists($firstPath));
    }

    public function test_test_score_double_submit_is_idempotent(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee);
        $todo = $this->makeTodo($course, 'TEST', ['passing_score' => 70]);

        $this->actingAs($employee, 'employee')
            ->post(route('todos.test', $todo), ['score' => 80])
            ->assertSessionHas('success');
        $this->actingAs($employee, 'employee')
            ->post(route('todos.test', $todo), ['score' => 60])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('course_todo_responses', 1);
        $this->assertDatabaseHas('course_todo_responses', [
            'enrollment_id' => $enrollment->id,
            'course_todo_id' => $todo->id,
            'score' => 60,
            'status' => 'FAILED',
        ]);
    }

    public function test_todo_submission_blocked_after_deadline(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee, [
            'enrollment_deadline' => now()->subDay()->toDateString(),
        ]);
        $todo = $this->makeTodo($course);

        $this->actingAs($employee, 'employee')
            ->post(route('todos.questionnaire', $todo), ['response_content' => 'x'])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('course_todo_responses', 0);
    }

    public function test_todo_submission_blocked_when_enrollment_cancelled(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee, [
            'status' => 'CANCELLED',
        ]);
        $todo = $this->makeTodo($course);

        $this->actingAs($employee, 'employee')
            ->post(route('todos.questionnaire', $todo), ['response_content' => 'x'])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('course_todo_responses', 0);
    }

    public function test_course_todo_response_unique_constraint_blocks_duplicates(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee);
        $todo = $this->makeTodo($course);

        CourseTodoResponse::create([
            'enrollment_id' => $enrollment->id,
            'course_todo_id' => $todo->id,
            'response_content' => 'x',
            'status' => 'PASSED',
        ]);

        $this->expectException(QueryException::class);

        CourseTodoResponse::create([
            'enrollment_id' => $enrollment->id,
            'course_todo_id' => $todo->id,
            'response_content' => 'y',
            'status' => 'PASSED',
        ]);
    }

    public function test_exam_start_reuses_in_progress_attempt(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee);
        $todo = $this->makeTodo($course, 'TEST');
        $question = $this->makeQuestion($course);
        QuestionOption::create([
            'question_id' => $question->id,
            'option_text' => 'A',
            'is_correct' => true,
        ]);

        $this->actingAs($employee, 'employee')
            ->get(route('exam.start', [$enrollment, $todo]))
            ->assertOk();
        $this->actingAs($employee, 'employee')
            ->get(route('exam.start', [$enrollment, $todo]))
            ->assertOk();

        $this->assertDatabaseCount('exam_attempts', 1);
        $this->assertDatabaseHas('exam_attempts', [
            'enrollment_id' => $enrollment->id,
            'course_todo_id' => $todo->id,
            'status' => 'IN_PROGRESS',
        ]);
    }

    public function test_exam_submit_is_atomic_and_double_submit_blocked(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee);
        $todo = $this->makeTodo($course, 'TEST', ['passing_score' => 5]);
        $question = $this->makeQuestion($course);
        $correct = QuestionOption::create([
            'question_id' => $question->id,
            'option_text' => 'A',
            'is_correct' => true,
        ]);
        $attempt = ExamAttempt::create([
            'enrollment_id' => $enrollment->id,
            'course_todo_id' => $todo->id,
            'attempt_number' => 1,
            'status' => 'IN_PROGRESS',
            'started_at' => now(),
            'max_score' => 10,
        ]);

        $this->actingAs($employee, 'employee')
            ->post(route('exam.submit', $attempt), ['question_'.$question->id => $correct->id])
            ->assertRedirect(route('attendance.score', $enrollment))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('exam_answers', 1);
        $this->assertDatabaseHas('exam_attempts', [
            'id' => $attempt->id,
            'status' => 'COMPLETED',
            'total_score' => 10,
        ]);
        $this->assertDatabaseHas('course_todo_responses', [
            'enrollment_id' => $enrollment->id,
            'course_todo_id' => $todo->id,
            'score' => 10,
            'status' => 'PASSED',
        ]);

        $this->actingAs($employee, 'employee')
            ->post(route('exam.submit', $attempt), ['question_'.$question->id => $correct->id])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('exam_answers', 1);
    }

    public function test_exam_submit_blocked_after_deadline(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee, [
            'enrollment_deadline' => now()->subDay()->toDateString(),
        ]);
        $todo = $this->makeTodo($course, 'TEST');
        $question = $this->makeQuestion($course);
        $attempt = ExamAttempt::create([
            'enrollment_id' => $enrollment->id,
            'course_todo_id' => $todo->id,
            'attempt_number' => 1,
            'status' => 'IN_PROGRESS',
            'started_at' => now(),
            'max_score' => 10,
        ]);

        $this->actingAs($employee, 'employee')
            ->post(route('exam.submit', $attempt), ['question_'.$question->id => null])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('exam_answers', 0);
        $this->assertDatabaseHas('exam_attempts', [
            'id' => $attempt->id,
            'status' => 'IN_PROGRESS',
        ]);
    }

    public function test_exam_attempt_unique_constraint_blocks_duplicates(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee);
        $todo = $this->makeTodo($course, 'TEST');

        ExamAttempt::create([
            'enrollment_id' => $enrollment->id,
            'course_todo_id' => $todo->id,
            'attempt_number' => 1,
            'status' => 'IN_PROGRESS',
            'started_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        ExamAttempt::create([
            'enrollment_id' => $enrollment->id,
            'course_todo_id' => $todo->id,
            'attempt_number' => 1,
            'status' => 'IN_PROGRESS',
            'started_at' => now(),
        ]);
    }

    public function test_exam_answer_unique_constraint_blocks_duplicates(): void
    {
        $company = $this->makeCompany();
        $employee = $this->makeEmployee($company);
        $course = $this->makeCourse();
        $enrollment = $this->makeEnrollment($course, $employee);
        $todo = $this->makeTodo($course, 'TEST');
        $question = $this->makeQuestion($course);
        $attempt = ExamAttempt::create([
            'enrollment_id' => $enrollment->id,
            'course_todo_id' => $todo->id,
            'attempt_number' => 1,
            'status' => 'IN_PROGRESS',
            'started_at' => now(),
        ]);

        ExamAnswer::create([
            'exam_attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'points_earned' => 0,
        ]);

        $this->expectException(QueryException::class);

        ExamAnswer::create([
            'exam_attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'points_earned' => 0,
        ]);
    }
}
