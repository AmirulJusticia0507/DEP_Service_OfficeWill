<?php

namespace Database\Seeders;

use App\Models\Affiliation;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Company;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseTodoResponse;
use App\Models\Employee;
use App\Models\EmployeeAffiliation;
use App\Models\MasterJob;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DemoCompleteDataSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first() ?? Company::create([
            'company_name' => 'PT Demo Center',
            'login_url' => 'http://localhost:8000/login',
        ]);

        $employees = [
            ['code' => 'EMP005', 'name' => 'Rina Wijaya', 'kana' => 'リナ・ウィジャヤ', 'email' => 'rina@demo.com', 'aff' => 'HRD', 'job' => 'STF'],
            ['code' => 'EMP006', 'name' => 'Andi Pratama', 'kana' => 'アンディ・プラタマ', 'email' => 'andi@demo.com', 'aff' => 'IT', 'job' => 'SPV'],
            ['code' => 'EMP007', 'name' => 'Maya Sari', 'kana' => 'マヤ・サリ', 'email' => 'maya@demo.com', 'aff' => 'FIN', 'job' => 'MGR'],
            ['code' => 'EMP008', 'name' => 'Joko Susilo', 'kana' => 'ジョコ・スシロ', 'email' => 'joko@demo.com', 'aff' => 'FC-001', 'job' => 'STF'],
            ['code' => 'EMP009', 'name' => 'Lina Marlina', 'kana' => 'リナ・マルリナ', 'email' => 'lina@demo.com', 'aff' => 'PST', 'job' => 'ADM'],
        ];

        $courses = Course::all();
        if ($courses->isEmpty()) {
            $this->command->warn('Tidak ada course. Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        $done = 0;

        foreach ($employees as $data) {
            $emp = Employee::updateOrCreate(
                ['company_id' => $company->id, 'employee_code' => $data['code']],
                [
                    'full_name' => $data['name'],
                    'kana_name' => $data['kana'],
                    'email' => $data['email'],
                    'password' => Hash::make('password123'),
                    'account_status' => 'ACTIVE',
                    'authority_effective_range' => 'ONLY',
                    'authority_effective_affiliation_code' => $data['aff'],
                    'place_of_birth' => 'Yogyakarta',
                    'date_of_birth' => now()->subYears(28),
                    'gender' => 'FEMALE',
                    'address' => 'Jl. Demo No. 1, Yogyakarta',
                ]
            );

            $affiliation = Affiliation::where('company_id', $company->id)
                ->where('affiliation_code', $data['aff'])->first();
            $job = MasterJob::where('company_id', $company->id)
                ->where('job_id', $data['job'])->first();

            if ($affiliation) {
                EmployeeAffiliation::updateOrCreate(
                    ['employee_id' => $emp->id, 'affiliation_code' => $data['aff']],
                    ['company_id' => $company->id, 'job_id' => $data['job'], 'start_date' => now()->subMonths(6)]
                );
            }

            foreach ($courses as $course) {
                $enrollment = CourseEnrollment::firstOrCreate(
                    ['course_id' => $course->id, 'employee_id' => $emp->id],
                    [
                        'enrollment_deadline' => now()->subDays(10)->toDateString(),
                        'status' => 'COMPLETED',
                        'created_at' => now()->subMonths(1),
                        'updated_at' => now()->subDays(5),
                    ]
                );
                $enrollment->forceFill(['status' => 'COMPLETED'])->save();

                foreach ($course->todos as $todo) {
                    $passing = $todo->passing_score ?? $course->passing_score ?? 70;
                    $score = $todo->todo_type === 'TEST' ? min(100, $passing + 8) : null;

                    CourseTodoResponse::updateOrCreate(
                        ['enrollment_id' => $enrollment->id, 'course_todo_id' => $todo->id],
                        [
                            'response_content' => $todo->todo_type === 'REPORT'
                                ? 'reports/demo-report-'.$enrollment->id.'.pdf'
                                : 'Demo response '.$emp->full_name.' - '.$todo->title,
                            'score' => $score,
                            'status' => 'PASSED',
                            'updated_at' => now()->subDays(5),
                        ]
                    );
                }

                Attendance::firstOrCreate(
                    ['employee_id' => $emp->id, 'enrollment_id' => $enrollment->id],
                    [
                        'course_id' => $course->id,
                        'status' => 'COMPLETED',
                        'attended_at' => now()->subDays(5),
                    ]
                );

                if (! Certificate::where('enrollment_id', $enrollment->id)->exists()) {
                    $certNumber = 'OW-YOG-'.str_pad($enrollment->id, 5, '0', STR_PAD_LEFT).'-'.now()->subDays(5)->format('Ymd');
                    $filename = 'certificates/'.$certNumber.'.pdf';

                    $pdf = Pdf::loadView('certificates.template', [
                        'employee' => $emp,
                        'course' => $course,
                        'certificate_number' => $certNumber,
                        'issued_at' => now()->subDays(5),
                    ]);
                    Storage::disk('public')->put($filename, $pdf->output());

                    Certificate::create([
                        'enrollment_id' => $enrollment->id,
                        'employee_id' => $emp->id,
                        'course_id' => $course->id,
                        'certificate_number' => $certNumber,
                        'file_path' => $filename,
                        'issued_at' => now()->subDays(5),
                    ]);
                }
            }

            $done++;
        }

        $this->command->info("Demo data selesai: {$done} karyawan lengkap sampai transkrip & sertifikat.");
    }
}
