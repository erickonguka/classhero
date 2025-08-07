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

        $certificate->load(['user', 'course']);
        
        // Create PDF
        $pdf = new \TCPDF();
        $pdf->SetCreator('ClassHero');
        $pdf->SetAuthor('ClassHero');
        $pdf->SetTitle('Certificate of Completion');
        $pdf->SetMargins(20, 20, 20);
        $pdf->AddPage();
        
        // Certificate content
        $html = '
        <div style="text-align: center; font-family: Arial, sans-serif;">
            <h1 style="color: #3B82F6; font-size: 36px; margin-bottom: 20px;">Certificate of Completion</h1>
            <p style="font-size: 18px; margin-bottom: 30px;">This certifies that</p>
            <h2 style="color: #1D4ED8; font-size: 32px; margin-bottom: 20px;">' . $certificate->user->name . '</h2>
            <p style="font-size: 18px; margin-bottom: 30px;">has successfully completed the course</p>
            <h3 style="color: #374151; font-size: 24px; margin-bottom: 40px;">' . $certificate->course->title . '</h3>
            <p style="font-size: 14px; color: #6B7280;">Certificate No: ' . $certificate->certificate_number . '</p>
            <p style="font-size: 14px; color: #6B7280;">Issued on: ' . $certificate->issued_at->format('F j, Y') . '</p>
            <p style="font-size: 14px; color: #6B7280; margin-top: 40px;">ClassHero E-Learning Platform</p>
        </div>';
        
        $pdf->writeHTML($html, true, false, true, false, '');
        
        return response($pdf->Output('certificate.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="certificate-' . $certificate->certificate_number . '.pdf"');
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