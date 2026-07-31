<?php

use App\Http\Controllers\Admin\AffiliationController;
use App\Http\Controllers\Admin\AuthorityController;
use App\Http\Controllers\Admin\CourseAssignmentController;
use App\Http\Controllers\Admin\CourseCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExamReportController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\MailLogController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\MfaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', [PublicController::class, 'welcome']);

Route::get('certificates/verify', [CertificateController::class, 'verifyForm'])->name('certificates.verify.form');
Route::get('certificates/verify/{certificateNumber}', [CertificateController::class, 'verify'])->name('certificates.verify');

Route::middleware('guest:employee')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::post('reissue-password', [AuthController::class, 'reissuePassword'])->name('reissue-password');
});

Route::get('mfa/verify', [MfaController::class, 'showVerifyForm'])->name('mfa.verify');
Route::post('mfa/verify', [MfaController::class, 'verify']);
Route::post('mfa/resend', [MfaController::class, 'resend'])->name('mfa.resend');
Route::post('mfa/cancel', [MfaController::class, 'cancel'])->name('mfa.cancel');

Route::middleware('auth:employee')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('change-password', [AuthController::class, 'showChangePasswordForm'])->name('change-password');
    Route::post('change-password', [AuthController::class, 'changePassword']);

    // Profile (self-service)
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('profile/transcript', [ProfileController::class, 'transcript'])->name('profile.transcript');
    Route::get('profile/certificates', [CertificateController::class, 'index'])->name('profile.certificates');

    // Certificates
    Route::post('certificates/generate/{enrollment}', [CertificateController::class, 'generate'])->name('certificates.generate');
    Route::get('certificates/download/{certificate}', [CertificateController::class, 'download'])->name('certificates.download');

    // Notifications (API)
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Exam
    Route::get('exam/start/{enrollment}/{todo}', [ExamController::class, 'start'])->name('exam.start');
    Route::post('exam/submit/{attempt}', [ExamController::class, 'submit'])->name('exam.submit');
    Route::post('exam/grade/{attempt}', [ExamController::class, 'grade'])->name('exam.grade');

    // CRUD resources
    Route::resource('employees', EmployeeController::class);
    Route::resource('courses', CourseController::class);
    Route::post('courses/{course}/materials', [CourseController::class, 'storeMaterial'])->name('courses.materials.store');
    Route::delete('materials/{material}', [CourseController::class, 'destroyMaterial'])->name('materials.destroy');
    Route::post('courses/{course}/todos', [CourseController::class, 'storeTodo'])->name('courses.todos.store');
    Route::delete('todos/{todo}', [CourseController::class, 'destroyTodo'])->name('todos.destroy');
    Route::resource('enrollments', EnrollmentController::class)->except(['create', 'store']);
    Route::post('enrollments/{enrollment}/send-confirmation', [EnrollmentController::class, 'sendConfirmation'])->name('enrollments.send-confirmation');

    // Attendance & completion
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/{course}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::get('attendance/{enrollment}/todos', [AttendanceController::class, 'todos'])->name('attendance.todos');
    Route::get('attendance/{enrollment}/score', [AttendanceController::class, 'score'])->name('attendance.score');
    Route::post('attendance/{enrollment}/complete', [AttendanceController::class, 'complete'])->name('attendance.complete');

    // Todo submissions
    Route::post('todos/{todo}/questionnaire', [TodoController::class, 'submitQuestionnaire'])->name('todos.questionnaire');
    Route::post('todos/{todo}/report', [TodoController::class, 'submitReport'])->name('todos.report');
    Route::post('todos/{todo}/test', [TodoController::class, 'submitTest'])->name('todos.test');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('authorities', [AuthorityController::class, 'index'])->name('authorities.index');
        Route::put('authorities/{employee}', [AuthorityController::class, 'update'])->name('authorities.update');

        Route::resource('affiliations', AffiliationController::class);
        Route::resource('positions', PositionController::class)->parameters(['positions' => 'position']);
        Route::resource('course-categories', CourseCategoryController::class)->parameters(['course-categories' => 'courseCategory']);
        Route::post('course-categories/{courseCategory}/details', [CourseCategoryController::class, 'storeDetail'])->name('course-categories.details.store');
        Route::delete('course-categories/details/{detail}', [CourseCategoryController::class, 'destroyDetail'])->name('course-categories.details.destroy');

        Route::get('assignments', [CourseAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('assignments/create', [CourseAssignmentController::class, 'create'])->name('assignments.create');
        Route::post('assignments', [CourseAssignmentController::class, 'store'])->name('assignments.store');
        Route::post('assignments/{enrollment}/cancel', [CourseAssignmentController::class, 'cancel'])->name('assignments.cancel');

        Route::get('inquiries/course', [InquiryController::class, 'byCourse'])->name('inquiries.course');
        Route::get('inquiries/employee', [InquiryController::class, 'byEmployee'])->name('inquiries.employee');
        Route::get('inquiries/todo-answers', [InquiryController::class, 'todoAnswers'])->name('inquiries.todo-answers');

        // Questions (Quiz Bank)
        Route::get('courses/{course}/questions', [QuestionController::class, 'index'])->name('questions.index');
        Route::get('courses/{course}/questions/create', [QuestionController::class, 'create'])->name('questions.create');
        Route::post('courses/{course}/questions', [QuestionController::class, 'store'])->name('questions.store');
        Route::get('courses/{course}/questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
        Route::put('courses/{course}/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
        Route::delete('courses/{course}/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');

        // Exam Reports
        Route::get('exam-reports/course', [ExamReportController::class, 'byCourse'])->name('exam-reports.by-course');
        Route::get('exam-reports/employee', [ExamReportController::class, 'byEmployee'])->name('exam-reports.by-employee');

        // Mail Log
        Route::get('mail-log', [MailLogController::class, 'index'])->name('mail-log.index');
        Route::get('mail-log/message/{id}', [MailLogController::class, 'show'])->name('mail-log.show');
        Route::put('mail-log/read-all', [MailLogController::class, 'markAllRead'])->name('mail-log.read-all');
        Route::put('mail-log/{id}/read', [MailLogController::class, 'markRead'])->name('mail-log.read');
        Route::delete('mail-log/{id}', [MailLogController::class, 'destroy'])->name('mail-log.destroy');
        Route::delete('mail-log', [MailLogController::class, 'destroyAll'])->name('mail-log.destroy-all');
    });

    Route::post('locale/switch', function (Request $request) {
        $locale = $request->input('locale', 'en');
        if (in_array($locale, ['en', 'ja', 'id'])) {
            Session::put('locale', $locale);
            App::setLocale($locale);
        }

        return redirect()->back();
    })->name('locale.switch');
});
