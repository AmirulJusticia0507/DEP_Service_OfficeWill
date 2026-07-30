<?php

use App\Http\Controllers\Admin\AffiliationController;
use App\Http\Controllers\Admin\CourseAssignmentController;
use App\Http\Controllers\Admin\CourseCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest:employee')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::post('reissue-password', [AuthController::class, 'reissuePassword'])->name('reissue-password');
});

Route::middleware('auth:employee')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('change-password', [AuthController::class, 'showChangePasswordForm'])->name('change-password');
    Route::post('change-password', [AuthController::class, 'changePassword']);

    Route::resource('employees', EmployeeController::class);

    Route::resource('courses', CourseController::class);
    Route::post('courses/{course}/materials', [CourseController::class, 'storeMaterial'])->name('courses.materials.store');
    Route::delete('materials/{material}', [CourseController::class, 'destroyMaterial'])->name('materials.destroy');
    Route::post('courses/{course}/todos', [CourseController::class, 'storeTodo'])->name('courses.todos.store');
    Route::delete('todos/{todo}', [CourseController::class, 'destroyTodo'])->name('todos.destroy');

    Route::resource('enrollments', EnrollmentController::class)->except(['create', 'store']);

    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/{course}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::get('attendance/{enrollment}/todos', [AttendanceController::class, 'todos'])->name('attendance.todos');
    Route::get('attendance/{enrollment}/score', [AttendanceController::class, 'score'])->name('attendance.score');
    Route::post('attendance/{enrollment}/complete', [AttendanceController::class, 'complete'])->name('attendance.complete');

    Route::post('todos/{todo}/questionnaire', [TodoController::class, 'submitQuestionnaire'])->name('todos.questionnaire');
    Route::post('todos/{todo}/report', [TodoController::class, 'submitReport'])->name('todos.report');
    Route::post('todos/{todo}/test', [TodoController::class, 'submitTest'])->name('todos.test');

    Route::prefix('admin')->name('admin.')->group(function () {
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
    });
});
