<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\CourseEnrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function verifyForm()
    {
        return view('certificates.verify', ['valid' => null]);
    }

    public function index()
    {
        $employee = Auth::guard('employee')->user();
        $certificates = Certificate::with('course')
            ->where('employee_id', $employee->id)
            ->orderBy('issued_at', 'desc')
            ->get();

        return view('profile.certificates', compact('certificates'));
    }

    public function generate(CourseEnrollment $enrollment): RedirectResponse
    {
        $employee = Auth::guard('employee')->user();
        if ($enrollment->employee_id !== $employee->id) {
            abort(403);
        }
        if ($enrollment->status !== 'COMPLETED') {
            return back()->with('error', 'Kursus belum selesai.');
        }

        $certNumber = 'OW-YOG-'.str_pad($enrollment->id, 5, '0', STR_PAD_LEFT).'-'.now()->format('Ymd');
        $filename = 'certificates/'.$certNumber.'.pdf';

        try {
            $created = DB::transaction(function () use ($enrollment, $employee, $certNumber, $filename) {
                $locked = CourseEnrollment::whereKey($enrollment->id)->lockForUpdate()->first();

                if ($locked->status !== 'COMPLETED') {
                    return false;
                }

                $existing = Certificate::where('enrollment_id', $locked->id)->first();
                if ($existing) {
                    return false;
                }

                $pdf = Pdf::loadView('certificates.template', [
                    'employee' => $employee,
                    'course' => $locked->course,
                    'certificate_number' => $certNumber,
                    'issued_at' => now(),
                ]);

                Storage::disk('public')->put($filename, $pdf->output());

                Certificate::create([
                    'enrollment_id' => $locked->id,
                    'employee_id' => $employee->id,
                    'course_id' => $locked->course_id,
                    'certificate_number' => $certNumber,
                    'file_path' => $filename,
                    'issued_at' => now(),
                ]);

                return true;
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($filename);
            throw $e;
        }

        return back()->with(
            $created ? 'success' : 'info',
            $created ? 'Sertifikat berhasil dibuat.' : 'Sertifikat sudah tersedia.'
        );
    }

    public function download(Certificate $certificate): Response
    {
        $employee = Auth::guard('employee')->user();
        if ($certificate->employee_id !== $employee->id) {
            abort(403);
        }

        if (! Storage::disk('public')->exists($certificate->file_path)) {
            abort(404);
        }

        $path = Storage::disk('public')->path($certificate->file_path);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($certificate->file_path).'"',
        ]);
    }

    public function verify(string $certificateNumber)
    {
        $cert = Certificate::with(['employee', 'course'])
            ->where('certificate_number', $certificateNumber)
            ->first();

        if (! $cert) {
            return view('certificates.verify', ['valid' => false]);
        }

        return view('certificates.verify', [
            'valid' => true,
            'certificate' => $cert,
            'employee' => $cert->employee,
            'course' => $cert->course,
        ]);
    }
}
