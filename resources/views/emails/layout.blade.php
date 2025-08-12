<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ClassHero')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6; 
            color: #1f2937; 
            background: #f8fafc;
            padding: 20px 0;
        }
        .email-wrapper {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            min-height: 100vh;
        }
        .email-container { 
            max-width: 650px; 
            margin: 0 auto; 
            background: white; 
            border-radius: 20px; 
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .header { 
            background: linear-gradient(135deg, #1e40af 0%, #7c3aed 100%);
            color: white; 
            padding: 50px 40px; 
            text-align: center;
            position: relative;
        }
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }
        .logo { 
            width: 80px; 
            height: 80px; 
            background: rgba(255, 255, 255, 0.15); 
            border-radius: 20px; 
            margin: 0 auto 24px; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            font-size: 32px;
            font-weight: 800;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        .header h1 { 
            font-size: 32px; 
            font-weight: 800; 
            margin-bottom: 12px;
            letter-spacing: -0.025em;
        }
        .header p { 
            font-size: 18px; 
            opacity: 0.9;
            font-weight: 500;
        }
        .content { 
            padding: 50px 40px; 
            background: white; 
        }
        .content h2 { 
            color: #111827; 
            font-size: 28px; 
            font-weight: 700; 
            margin-bottom: 24px;
            letter-spacing: -0.025em;
        }
        .content p, .content div { 
            margin-bottom: 20px; 
            font-size: 17px; 
            line-height: 1.7;
            color: #374151;
        }
        .content ul, .content ol {
            margin: 20px 0;
            padding-left: 24px;
        }
        .content li {
            margin-bottom: 8px;
            font-size: 17px;
            line-height: 1.7;
        }
        .button { 
            display: inline-block; 
            background: linear-gradient(135deg, #1e40af 0%, #7c3aed 100%);
            color: white !important; 
            padding: 18px 36px; 
            text-decoration: none; 
            border-radius: 12px; 
            font-weight: 600; 
            font-size: 16px;
            margin: 24px 0;
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
            transition: all 0.3s ease;
        }
        .button:hover { 
            transform: translateY(-2px);
            box-shadow: 0 15px 35px -5px rgba(59, 130, 246, 0.5);
        }
        .highlight-box { 
            background: linear-gradient(135deg, #eff6ff 0%, #f3e8ff 100%); 
            border: 1px solid #e0e7ff;
            border-left: 4px solid #3b82f6; 
            padding: 24px; 
            margin: 24px 0; 
            border-radius: 12px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 16px;
            margin: 24px 0;
        }
        .stat-item {
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }
        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 4px;
        }
        .stat-label {
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }
        .footer { 
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); 
            padding: 40px; 
            text-align: center; 
            border-top: 1px solid #e2e8f0;
        }
        .footer p { 
            color: #64748b; 
            font-size: 15px; 
            margin-bottom: 12px; 
        }
        .footer-links { 
            margin: 24px 0;
        }
        .footer-links a { 
            color: #475569;
            text-decoration: none;
            margin: 0 12px;
            font-weight: 500;
        }
        @media (max-width: 600px) {
            .email-wrapper { padding: 20px 10px; }
            .header, .content, .footer { padding: 30px 24px; }
            .header h1 { font-size: 28px; }
            .content h2 { font-size: 24px; }
            .logo { width: 60px; height: 60px; font-size: 24px; }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="header">
                <div class="logo">CH</div>
                <h1>@yield('header-title', 'ClassHero')</h1>
                <p>@yield('header-subtitle', 'Modern E-Learning Platform')</p>
            </div>
            
            <div class="content">
                @yield('content')
            </div>
            
            <div class="footer">
                <p><strong>ClassHero</strong> - Empowering learners worldwide 🚀</p>
                <p>This email was sent because you're part of our learning community.</p>
                
                <div class="footer-links">
                    <a href="{{ url('/') }}">Visit Platform</a>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Contact Support</a>
                </div>
                
                <p style="margin-top: 24px; font-size: 13px; color: #9ca3af;">
                    © {{ date('Y') }} ClassHero. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>