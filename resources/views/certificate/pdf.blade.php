<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate of Completion</title>
    <style>
        @page { 
            /* size: A4 landscape;  */
            margin: 0; 
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            /* width: 297mm;
            height: 210mm; */
            background: #fef3c7;
            position: relative;
        }
        
        .page {
            /* width: 297mm;
            height: 210mm; */
            padding: 10mm;
            position: relative;
        }
        
        .certificate {
            /* width: 277mm; */
            height: 165mm;
            background: #ffffff;
            border: 2mm solid #facc15;
            position: relative;
            padding: 10mm;
        }
        
        /* Decorative elements */
        .decor {
            position: absolute;
            border-radius: 50%;
            background: #fde68a;
            opacity: 0.3;
            width: 30mm;
            height: 30mm;
        }
        
        .decor.top-left { 
            top: -15mm; 
            left: -15mm; 
        }
        
        .decor.bottom-right { 
            bottom: -15mm; 
            right: -15mm; 
        }

        /* Header Section */
        .header { 
            text-align: center; 
            margin-bottom: 8mm;
        }
        
        .icon-circle {
            width: 12mm;
            height: 12mm;
            background: #facc15;
            border-radius: 50%;
            margin: 0 auto 4mm;
            position: relative;
            color: #1f2937;
            font-size: 6mm;
            font-weight: bold;
            text-align: center;
            padding-top: 3mm;
        }
                
        .header-title { 
            font-size: 12mm; 
            font-weight: bold; 
            color: #1f2937; 
            margin: 0 0 3mm 0;
            text-align: center;
        }
        
        .header-sub { 
            font-size: 4mm; 
            color: #4b5563; 
            margin: 0;
            text-align: center;
        }

        /* Name Section */
        .name-section { 
            text-align: center;
            margin: 12mm 0;
        }
        
        .student-name {
            font-size: 14mm;
            font-weight: bold;
            color: #b45309;
            margin: 0 0 3mm 0;
            text-align: center;
            line-height: 1.2;
        }
        
        .name-sub { 
            font-size: 4mm; 
            color: #4b5563; 
            margin: 0;
            text-align: center;
        }

        /* Course Section */
        .course-section { 
            text-align: center;
            margin: 10mm 0;
        }
        
        .course-title { 
            font-size: 8mm; 
            font-weight: bold; 
            color: #1f2937; 
            margin: 0 0 4mm 0;
            text-align: center;
            line-height: 1.3;
        }
        
        .course-meta { 
            font-size: 3.5mm; 
            color: #4b5563; 
            text-align: center;
            margin: 0;
        }
        
        .course-meta span {
            margin: 0 4mm;
        }

        /* Footer Section */
        .footer {
            position: absolute;
            bottom: 25mm;
            left: 15mm;
            right: 15mm;
            height: 20mm;
        }
        
        .footer-row {
            width: 100%;
            margin: 0;
            padding: 0;
        }
        
        .footer-item {
            width: 33.333%;
            float: left;
            text-align: center;
            font-size: 3.5mm;
            padding: 0 2mm;
        }
        
        .footer-line { 
            height: 0.5mm; 
            background: #9ca3af; 
            width: 30mm; 
            margin: 0 auto 2mm; 
        }
        
        .footer-label { 
            color: #4b5563; 
            margin-bottom: 1mm;
            font-size: 3mm;
        }
        
        .footer-value { 
            font-weight: bold; 
            color: #1f2937; 
            font-size: 4mm;
        }

        .platform-logo {
            width: 12mm;
            height: 12mm;
            border-radius: 50%;
            background: #3b82f6;
            margin: 0 auto 2mm;
            color: white;
            font-weight: bold;
            font-size: 4mm;
            text-align: center;
            padding-top: 3mm;
        }
        
        .platform-name {
            font-size: 4mm;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 1mm;
        }
        
        .platform-sub {
            font-size: 2.5mm; 
            color: #6b7280;
        }

        /* Details Section */
        .details {
            position: absolute;
            bottom: 5mm;
            left: 15mm;
            right: 15mm;
            font-size: 3mm;
            color: #6b7280;
            border-top: 0.5mm solid #e5e7eb;
            padding-top: 2mm;
        }
        
        .detail-left {
            float: left;
        }
        
        .detail-right {
            float: right;
        }
        
        /* Clear floats */
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
        
        /* PDF specific fixes */
        table {
            border-collapse: collapse;
            width: 100%;
        }
        
        td {
            vertical-align: top;
            padding: 0;
        }

        /* Link styling for PDF */
        a {
            color: #3b82f6;
            text-decoration: underline;
        }

        .details a {
            font-size: 3mm;
            color: #3b82f6;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="certificate">
            <div class="decor top-left"></div>
            <div class="decor bottom-right"></div>

            <!-- Header -->
            <div class="header">
                <div class="icon-circle">✓</div>
                <h1 class="header-title">Certificate of Completion</h1>
                <p class="header-sub">This certifies that</p>
            </div>

            <!-- Student Name -->
            <div class="name-section">
                <h2 class="student-name">{{ $certificate->user->name }}</h2>
                <p class="name-sub">has successfully completed the course</p>
            </div>

            <!-- Course Info -->
            <div class="course-section">
                <h3 class="course-title">{{ $certificate->course->title }}</h3>
                <div class="course-meta">
                    <span>📖 {{ $certificate->course->lessons->count() }} Lessons</span>
                    @if($certificate->course->duration_hours)
                        <span>🕔 {{ $certificate->course->duration_hours }} Hours</span>
                    @endif
                    <span>⚡ {{ ucfirst($certificate->course->difficulty) }}</span>
                </div>
            </div>

            <!-- Footer with 3 columns -->
            <div class="footer">
                <div class="footer-row clearfix">
                    <!-- Instructor -->
                    <div class="footer-item">
                        <div class="footer-line"></div>
                        <div class="footer-label">Instructor</div>
                        <div class="footer-value">{{ $certificate->course->teacher->name }}</div>
                    </div>
                    
                    <!-- Platform -->
                    <div class="footer-item">
                        <div class="platform-logo">CH</div>
                        <div class="platform-name">ClassHero</div>
                        <div class="platform-sub">E-Learning Platform</div>
                    </div>
                    
                    <!-- Date -->
                    <div class="footer-item">
                        <div class="footer-line"></div>
                        <div class="footer-label">Date Completed</div>
                        <div class="footer-value">{{ $certificate->issued_at->format('M j, Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Certificate Details -->
            <div class="details clearfix">
                <div class="detail-left">Certificate No: {{ $certificate->certificate_number }}</div>
                <div class="detail-right">
                    Verify at: <a href="{{ url('/certificate/verify/' . $certificate->verification_code) }}" style="color: #3b82f6; text-decoration: underline;">
                        {{ config('app.url') }}/verify/{{ substr($certificate->verification_code, 0, 8) }}...
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>