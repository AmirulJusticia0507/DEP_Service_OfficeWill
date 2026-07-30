<?php

namespace App\Actions\Course;

use App\Models\Course;
use App\Models\Employee;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\DB;

class AssignCourseToEmployeesAction
{
    public function execute(Course $course, array $employeeIds, string $deadline): int
    {
        $count = 0;
        DB::transaction(function () use ($course, $employeeIds, $deadline, &$count) {
            foreach ($employeeIds as $empId) {
                $exists = CourseEnrollment::where('course_id', $course->id)
                    ->where('employee_id', $empId)
                    ->exists();

                if (!$exists) {
                    CourseEnrollment::create([
                        'course_id' => $course->id,
                        'employee_id' => $empId,
                        'enrollment_deadline' => $deadline,
                        'status' => 'ENROLLED',
                    ]);
                    $count++;
                }
            }
        });

        return $count;
    }
}
