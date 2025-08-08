<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate of Completion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        /* Tip for professional results: For some PDF renderers, you may need to
           embed the font file (e.g., as a Base64 string) instead of linking to Google Fonts. */
        body {
            font-family: 'Poppins', 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .certificate-wrapper {
            width: 297mm;
            height: 210mm;
            padding: 10mm;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .certificate-container {
            width: 100%;
            height: 100%;
            background: white;
            border: 4px solid #ffc107;
            border-radius: 15px;
            padding: 30px;
            box-sizing: border-box;
            position: relative;
            text-align: center;
        }

        /* Decorative corner shapes */
        .certificate-container::before,
        .certificate-container::after {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            background-color: #ffc107;
            opacity: 0.1;
            z-index: 1;
            border-radius: 50%;
        }

        .certificate-container::before { top: -50px; left: -50px; }
        .certificate-container::after { bottom: -50px; right: -50px; }
        
        .content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: table;
            width: 100%;
        }
        
        .content-cell {
            display: table-cell;
            vertical-align: middle;
        }

        .badge {
            width: 60px;
            height: 60px;
            background-color: #ffc107;
            border-radius: 50%;
            margin: 0 auto 15px auto;
            line-height: 60px;
            font-size: 30px;
            color: white;
        }

        .main-title {
            font-size: 32px;
            font-weight: 600;
            color: #343a40;
            margin: 0;
        }

        .subtitle {
            font-size: 16px;
            color: #6c757d;
            margin: 5px 0 15px 0;
        }

        .recipient-name {
            font-size: 44px;
            font-weight: 700;
            color: #f58220;
            margin: 0;
        }

        .completion-text {
            font-size: 16px;
            color: #495057;
            margin: 5px 0 20px 0;
        }

        .course-title {
            font-size: 26px;
            font-weight: 600;
            color: #343a40;
            margin: 0;
        }

        .course-meta {
            margin: 15px 0 30px 0;
            font-size: 14px;
            color: #6c757d;
        }
        
        .meta-item {
            display: inline-block;
            margin: 0 12px;
        }
        .meta-item span { vertical-align: middle; }

        /* Robust table-based footer */
        .footer-details {
            display: table;
            width: 100%;
            table-layout: fixed;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }

        .footer-column {
            display: table-cell;
            width: 33.33%;
            vertical-align: top;
        }

        .footer-label {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .footer-value {
            font-size: 14px;
            font-weight: 600;
            color: #343a40;
        }
        
        .platform-logo {
            width: 45px;
            height: 45px;
            background-color: #6f42c1;
            border-radius: 50%;
            margin: 0 auto 8px auto;
            line-height: 45px;
            color: white;
            font-size: 18px;
            font-weight: 700;
        }

        .certificate-info {
            position: absolute;
            bottom: 10px;
            left: 30px;
            right: 30px;
            z-index: 3;
            font-size: 9px;
            color: #adb5bd;
        }
        .cert-no { float: left; }
        .ver-code { float: right; }

    </style>
</head>
<body>
    <div class="certificate-wrapper">
        <div class="certificate-container">
            <div class="content">
                <div class="content-cell">
                    <div class="badge">✔</div>

                    <h1 class="main-title">Certificate of Completion</h1>
                    <p class="subtitle">This certifies that</p>

                    <h2 class="recipient-name">{{ $certificate->user->name ?? 'Alice Wilson' }}</h2>
                    <p class="completion-text">has successfully completed the course</p>

                    <h3 class="course-title">{{ $certificate->course->title ?? 'iOS App Development with Swift' }}</h3>

                    <div class="course-meta">
                        @if($certificate->course->lessons)
                        <div class="meta-item">
                            <span>📖</span>
                            <span>{{ $certificate->course->lessons->count() ?? 3 }} Lessons</span>
                        </div>
                        @endif
                        @if($certificate->course->duration_hours)
                        <div class="meta-item">
                            <span>🕔</span>
                            <span>{{ $certificate->course->duration_hours ?? 50 }} Hours</span>
                        </div>
                        @endif
                        @if($certificate->course->difficulty)
                        <div class="meta-item">
                            <span>⚡️</span>
                            <span>{{ ucfirst($certificate->course->difficulty) ?? 'Advanced' }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="footer-details">
                        <div class="footer-column">
                            <p class="footer-label">Instructor</p>
                            <p class="footer-value">{{ $certificate->course->teacher->name ?? 'Sarah Johnson' }}</p>
                        </div>
                        <div class="footer-column">
                            <div class="platform-logo">CH</div>
                            <p class="footer-value" style="line-height: 1.2;">{{ $certificate->course->platform_name ?? 'ClassHero' }}<br><span style="font-size: 11px; color: #6c757d; font-weight: normal;">E-Learning Platform</span></p>
                        </div>
                        <div class="footer-column">
                            <p class="footer-label">Date Completed</p>
                            <p class="footer-value">{{ isset($certificate->issued_at) ? $certificate->issued_at->format('M j, Y') : 'Aug 8, 2025' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="certificate-info">
                <span class="cert-no">Certificate No: {{ $certificate->certificate_number ?? 'CERT-4UD8OM4Z' }}</span>
                <span class="ver-code">Verification Code: {{ isset($certificate->verification_code) ? substr($certificate->verification_code, 0, 10) : 'KMqSH3xm' }}...</span>
            </div>
        </div>
    </div>
</body>
</html>