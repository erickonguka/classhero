<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    public function generate(Course $course)
    {
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('progress_percentage', 100)
            ->first();

        if (!$enrollment) {
            return redirect()->back()->with('error', 'You must complete the course to generate a certificate.');
        }

        // Check if certificate already exists
        $existingCertificate = Certificate::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        if ($existingCertificate) {
            return redirect()->route('certificate.show', $existingCertificate);
        }

        // Generate certificate
        $certificate = Certificate::create([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'certificate_number' => 'CERT-' . strtoupper(Str::random(8)),
            'verification_code' => Str::random(32),
            'issued_at' => now(),
        ]);

        return redirect()->route('certificate.show', $certificate)
            ->with('success', 'Certificate generated successfully!');
    }

    public function show(Certificate $certificate)
    {
        if ($certificate->user_id !== Auth::id()) {
            abort(403);
        }

        $certificate->load(['user', 'course']);
        return view('certificate.show', compact('certificate'));
    }

    public function download(Certificate $certificate)
    {
        if ($certificate->user_id !== Auth::id()) {
            abort(403);
        }

        $certificate->load(['user', 'course.teacher', 'course.lessons']);

        $pdf = \PDF::loadView('certificate.pdf', compact('certificate'))
            ->setPaper('A4', 'landscape')
            ->setOptions([
                'dpi' => 96, // Lower DPI for better compatibility
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => false, // Disable for better compatibility
                'isPhpEnabled' => true,
                'isRemoteEnabled' => false,
                'enable_font_subsetting' => false,
                'pdf_backend' => 'CPDF',
                'tempDir' => storage_path('app/temp'),
            ]);

        return $pdf->download('certificate-' . $certificate->certificate_number . '.pdf');
    }


    public function verify($verificationCode)
    {
        $certificate = Certificate::where('verification_code', $verificationCode)->first();

        if (!$certificate) {
            return view('certificate.verify', ['certificate' => null, 'error' => 'Invalid verification code.']);
        }

        $certificate->load(['user', 'course']);
        return view('certificate.verify', compact('certificate'));
    }
}