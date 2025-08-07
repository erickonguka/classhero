<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate of Completion</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            font-family: 'Georgia', serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: #1a202c;
            width: 297mm;
            height: 210mm;
            position: relative;
        }
        .certificate {
            width: 100%;
            height: 100%;
            background: white;
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 0 100px rgba(0,0,0,0.05);
        }
        
        /* Elegant border system */
        .outer-border {
            position: absolute;
            top: 12mm;
            left: 12mm;
            right: 12mm;
            bottom: 12mm;
            border: 3px solid #d4af37;
            border-radius: 15px;
            background: linear-gradient(45deg, #ffd700 0%, #d4af37 100%);
            padding: 3px;
        }
        .main-border {
            width: 100%;
            height: 100%;
            border: 2px solid #8b7355;
            border-radius: 12px;
            background: white;
            position: relative;
        }
        .inner-frame {
            position: absolute;
            top: 8mm;
            left: 8mm;
            right: 8mm;
            bottom: 8mm;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: linear-gradient(to bottom, #ffffff 0%, #fafbfc 100%);
        }
        
        /* Decorative corner elements */
        .corner-ornament {
            position: absolute;
            width: 25mm;
            height: 25mm;
            background: radial-gradient(circle, #d4af37 20%, transparent 70%);
            opacity: 0.1;
        }
        .corner-ornament.top-left { top: 18mm; left: 18mm; }
        .corner-ornament.top-right { top: 18mm; right: 18mm; }
        .corner-ornament.bottom-left { bottom: 18mm; left: 18mm; }
        .corner-ornament.bottom-right { bottom: 18mm; right: 18mm; }
        
        /* Elegant flourishes */
        .flourish {
            position: absolute;
            font-size: 48px;
            color: #d4af37;
            opacity: 0.3;
        }
        .flourish.top { top: 25mm; left: 50%; transform: translateX(-50%); }
        .flourish.bottom { bottom: 25mm; left: 50%; transform: translateX(-50%); }
        
        /* Content styling */
        .content {
            position: absolute;
            top: 35mm;
            left: 35mm;
            right: 35mm;
            bottom: 35mm;
            text-align: center;
            z-index: 10;
        }
        
        .header {
            margin-bottom: 15mm;
        }
        .institution-logo {
            width: 15mm;
            height: 15mm;
            background: linear-gradient(45deg, #1e40af, #3b82f6);
            border-radius: 50%;
            margin: 0 auto 8mm;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: bold;
        }
        .institution-name {
            font-size: 24px;
            font-weight: 600;
            color: #1e40af;
            letter-spacing: 3px;
            margin-bottom: 3mm;
            text-transform: uppercase;
        }
        .certificate-title {
            font-size: 38px;
            font-weight: bold;
            background: linear-gradient(45deg, #d4af37, #b8860b);
            background-clip: text;
            -webkit-background-clip: text;
            color: #d4af37;
            margin: 8mm 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
        }
        .certificate-title::before,
        .certificate-title::after {
            content: '❖';
            position: absolute;
            top: 50%;
            color: #d4af37;
            font-size: 16px;
            opacity: 0.6;
        }
        .certificate-title::before { left: -25mm; transform: translateY(-50%); }
        .certificate-title::after { right: -25mm; transform: translateY(-50%); }
        
        .certification-text {
            font-size: 16px;
            color: #4a5568;
            font-style: italic;
            margin-bottom: 12mm;
            line-height: 1.4;
        }
        
        /* Recipient section */
        .recipient-section {
            margin: 12mm 0;
            position: relative;
        }
        .recipient-name {
            font-size: 32px;
            font-weight: bold;
            color: #1a202c;
            margin: 8mm 0;
            padding: 8mm 15mm;
            background: linear-gradient(to right, transparent, #f7fafc, transparent);
            border-top: 2px solid #d4af37;
            border-bottom: 2px solid #d4af37;
            position: relative;
        }
        .recipient-name::before,
        .recipient-name::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 20mm;
            height: 1px;
            background: linear-gradient(to right, #d4af37, transparent);
            transform: translateY(-50%);
        }
        .recipient-name::before { left: -25mm; }
        .recipient-name::after { right: -25mm; transform: translateY(-50%) rotate(180deg); }
        
        /* Course section */
        .course-section {
            margin: 15mm 0;
        }
        .course-intro {
            font-size: 14px;
            color: #4a5568;
            margin-bottom: 6mm;
            font-style: italic;
        }
        .course-title {
            font-size: 22px;
            font-weight: 600;
            color: #1e40af;
            margin: 6mm 0;
            line-height: 1.3;
            max-width: 180mm;
            margin-left: auto;
            margin-right: auto;
        }
        .course-details {
            font-size: 12px;
            color: #6b7280;
            margin: 8mm 0;
            display: flex;
            justify-content: center;
            gap: 15mm;
        }
        .detail-item {
            display: flex;
            align-items: center;
            gap: 2mm;
        }
        .completion-date {
            font-size: 14px;
            color: #374151;
            font-weight: 500;
            margin-top: 8mm;
        }
        
        /* Footer elements */
        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 25mm;
            display: flex;
            justify-content: space-between;
            align-items: end;
            padding: 0 25mm 8mm;
        }
        
        .signature-section {
            text-align: center;
            min-width: 60mm;
        }
        .signature-line {
            width: 50mm;
            height: 2px;
            background: linear-gradient(to right, #d4af37, #b8860b);
            margin: 12mm auto 4mm;
            position: relative;
        }
        .signature-line::before {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -2px;
            transform: translateX(-50%);
            width: 6mm;
            height: 2px;
            background: #d4af37;
        }
        .signature-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .instructor-name {
            font-size: 13px;
            font-weight: 600;
            color: #1a202c;
            margin-top: 2mm;
        }
        
        .certificate-seal {
            width: 40mm;
            height: 40mm;
            border: 3px solid #d4af37;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle, #ffffff, #f8fafc);
            position: relative;
            margin: 0 20mm;
        }
        .seal-text {
            font-size: 10px;
            font-weight: bold;
            color: #1e40af;
            text-align: center;
            line-height: 1.2;
        }
        .seal-year {
            font-size: 8px;
            color: #6b7280;
            margin-top: 1mm;
        }
        
        .certificate-info {
            text-align: right;
            font-size: 9px;
            color: #9ca3af;
            line-height: 1.5;
            min-width: 60mm;
        }
        .info-label {
            color: #6b7280;
            font-weight: 500;
        }
        
        /* Verification section */
        .verification {
            position: absolute;
            bottom: 3mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            background: linear-gradient(to right, transparent, #f8fafc, transparent);
            padding: 2mm 0;
        }
        
        /* Subtle background pattern */
        .certificate::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 25% 25%, #f0f9ff 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, #fef7e0 0%, transparent 50%);
            opacity: 0.4;
            z-index: 1;
        }
        
        /* Ensure content is above background */
        .outer-border {
            z-index: 5;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="corner-ornament top-left"></div>
        <div class="corner-ornament top-right"></div>
        <div class="corner-ornament bottom-left"></div>
        <div class="corner-ornament bottom-right"></div>
        
        <div class="flourish top">❦</div>
        <div class="flourish bottom">❦</div>
        
        <div class="outer-border">
            <div class="main-border">
                <div class="inner-frame">
                    <div class="content">
                        <div class="header">
                            <div class="institution-logo">CH</div>
                            <div class="institution-name">ClassHero</div>
                            <div class="certificate-title">Certificate of Completion</div>
                            <div class="certification-text">
                                This is to certify that the individual named below<br>
                                has successfully completed all requirements for the designated course
                            </div>
                        </div>
                        
                        <div class="recipient-section">
                            <div class="recipient-name">{{ strtoupper($certificate->user->name) }}</div>
                        </div>
                        
                        <div class="course-section">
                            <div class="course-intro">has demonstrated proficiency in</div>
                            <div class="course-title">{{ $certificate->course->title }}</div>
                            
                            @if($certificate->course->lessons || $certificate->course->duration_hours || $certificate->course->difficulty)
                            <div class="course-details">
                                @if($certificate->course->lessons)
                                <div class="detail-item">
                                    <span>📚</span>
                                    <span>{{ $certificate->course->lessons->count() ?? $certificate->course->lesson_count ?? 'Multiple' }} Lessons</span>
                                </div>
                                @endif
                                @if($certificate->course->duration_hours)
                                <div class="detail-item">
                                    <span>⏱️</span>
                                    <span>{{ $certificate->course->duration_hours }} Hours</span>
                                </div>
                                @endif
                                @if($certificate->course->difficulty)
                                <div class="detail-item">
                                    <span>⚡</span>
                                    <span>{{ ucfirst($certificate->course->difficulty) }} Level</span>
                                </div>
                                @endif
                            </div>
                            @endif
                            
                            <div class="completion-date">
                                Completed on {{ $certificate->issued_at->format('F j, Y') }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="footer">
                        <div class="signature-section">
                            <div class="signature-line"></div>
                            <div class="signature-label">Course Instructor</div>
                            <div class="instructor-name">{{ $certificate->course->teacher->name }}</div>
                        </div>
                        
                        <div class="certificate-seal">
                            <div class="seal-text">
                                CERTIFIED<br>
                                ACHIEVEMENT
                            </div>
                            <div class="seal-year">{{ $certificate->issued_at->format('Y') }}</div>
                        </div>
                        
                        <div class="certificate-info">
                            <div><span class="info-label">Certificate No:</span> {{ $certificate->certificate_number }}</div>
                            <div><span class="info-label">Issue Date:</span> {{ $certificate->issued_at->format('M j, Y') }}</div>
                            @if($certificate->course->duration_hours)
                            <div><span class="info-label">Course Duration:</span> {{ $certificate->course->duration_hours }} Hours</div>
                            @else
                            <div><span class="info-label">Course Type:</span> Self-paced</div>
                            @endif
                            <div><span class="info-label">Credits:</span> {{ $certificate->course->duration_hours ?? 'N/A' }}</div>
                        </div>
                    </div>
                    
                    <div class="verification">
                        Verify authenticity at: {{ url('/certificate/verify/' . $certificate->verification_code) }} | 
                        Verification Code: {{ substr($certificate->verification_code, 0, 12) }}...
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>