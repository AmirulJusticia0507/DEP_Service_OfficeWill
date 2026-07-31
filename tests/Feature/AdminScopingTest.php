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
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminScopingTest extends TestCase
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
            'course_name' => 'Course '.uniqid(),
        ]);
    }

    private function enroll(Course $course, Employee $employee): CourseEnrollment
    {
        return CourseEnrollment::create([
            'course_id' => $course->id,
            'employee_id' => $employee->id,
            'enrollment_deadline' => now()->addWeek()->toDateString(),
            'status' => 'ENROLLED',
        ]);
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

    private function makeOption(Question $question, string $text): QuestionOption
    {
        return QuestionOption::create([
            'question_id' => $question->id,
            'option_text' => $text,
            'is_correct' => false,
            'display_order' => 0,
        ]);
    }

    public function test_inquiry_by_employee_within_company_ok(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company, ['can_register_course' => true]);
        $employee = $this->makeEmployee($company, ['full_name' => 'Same Company Staff']);
        $course = $this->makeCourse();
        $this->enroll($course, $employee);

        $this->actingAs($operator, 'employee')
            ->get(route('admin.inquiries.employee', ['employee_id' => $employee->id]))
            ->assertOk()
            ->assertSee('Same Company Staff')
            ->assertSee($course->course_name);
    }

    public function test_inquiry_by_employee_rejects_foreign_company(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company, ['can_register_course' => true]);

        $otherCompany = $this->makeCompany();
        $foreign = $this->makeEmployee($otherCompany, ['full_name' => 'Foreign Staff']);
        $course = $this->makeCourse();
        $this->enroll($course, $foreign);

        $this->actingAs($operator, 'employee')
            ->get(route('admin.inquiries.employee', ['employee_id' => $foreign->id]))
            ->assertNotFound();
    }

    public function test_todo_answers_only_show_own_company_responses(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company, ['can_register_course' => true]);
        $own = $this->makeEmployee($company, ['full_name' => 'Own Company Staff']);
        $otherCompany = $this->makeCompany();
        $foreign = $this->makeEmployee($otherCompany, ['full_name' => 'Foreign Staff Leak']);

        $course = $this->makeCourse();
        $todo = CourseTodo::create([
            'course_id' => $course->id,
            'todo_type' => 'QUESTIONNAIRE',
            'title' => 'Survey Todo',
        ]);

        CourseTodoResponse::create([
            'course_todo_id' => $todo->id,
            'enrollment_id' => $this->enroll($course, $own)->id,
            'response_content' => 'own answer',
        ]);
        CourseTodoResponse::create([
            'course_todo_id' => $todo->id,
            'enrollment_id' => $this->enroll($course, $foreign)->id,
            'response_content' => 'foreign answer secret',
        ]);

        $this->actingAs($operator, 'employee')
            ->get(route('admin.inquiries.todo-answers', [
                'course_id' => $course->id,
                'todo_id' => $todo->id,
            ]))
            ->assertOk()
            ->assertSee('Own Company Staff')
            ->assertSee('own answer')
            ->assertDontSee('Foreign Staff Leak')
            ->assertDontSee('foreign answer secret');
    }

    public function test_exam_report_by_employee_rejects_foreign_company(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company, ['can_register_course' => true]);

        $otherCompany = $this->makeCompany();
        $foreign = $this->makeEmployee($otherCompany);
        $course = $this->makeCourse();
        $enrollment = $this->enroll($course, $foreign);
        $todo = CourseTodo::create([
            'course_id' => $course->id,
            'todo_type' => 'EXAM',
            'title' => 'Exam',
        ]);

        ExamAttempt::create([
            'course_todo_id' => $todo->id,
            'enrollment_id' => $enrollment->id,
            'attempt_number' => 1,
            'status' => 'COMPLETED',
            'total_score' => 80,
            'max_score' => 100,
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        $this->actingAs($operator, 'employee')
            ->get(route('admin.exam-reports.by-employee', ['employee_id' => $foreign->id]))
            ->assertNotFound();
    }

    public function test_question_edit_and_destroy_reject_cross_course(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company, ['can_register_course' => true]);

        $courseA = $this->makeCourse();
        $courseB = $this->makeCourse();
        $question = $this->makeQuestion($courseA, ['question_text' => 'Belongs to A']);

        $this->actingAs($operator, 'employee')
            ->get(route('admin.questions.edit', ['course' => $courseB, 'question' => $question]))
            ->assertNotFound();

        $this->actingAs($operator, 'employee')
            ->delete(route('admin.questions.destroy', ['course' => $courseB, 'question' => $question]))
            ->assertNotFound();

        $this->assertDatabaseHas('questions', ['id' => $question->id]);
    }

    public function test_question_update_cannot_modify_option_of_other_question(): void
    {
        $company = $this->makeCompany();
        $operator = $this->makeEmployee($company, ['can_register_course' => true]);

        $course = $this->makeCourse();
        $questionA = $this->makeQuestion($course, ['question_text' => 'Question A']);
        $questionB = $this->makeQuestion($course, ['question_text' => 'Question B']);
        $optionA = $this->makeOption($questionA, 'Original A');
        $optionB = $this->makeOption($questionB, 'Original B');

        $this->actingAs($operator, 'employee')
            ->put(route('admin.questions.update', ['course' => $course, 'question' => $questionA]), [
                'question_type' => 'MCQ',
                'question_text' => 'Question A updated',
                'points' => 10,
                'display_order' => 0,
                'options' => [
                    ['id' => $optionB->id, 'option_text' => 'Tampered B', 'is_correct' => true],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('question_options', [
            'id' => $optionB->id,
            'option_text' => 'Original B',
            'is_correct' => false,
        ]);
    }
}
