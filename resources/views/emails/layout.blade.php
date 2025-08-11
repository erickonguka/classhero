<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ClassHero')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6; 
            color: #374151; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 0;
        }
        .email-container { 
            max-width: 600px; 
            margin: 0 auto; 
            background: white; 
            border-radius: 16px; 
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .header { 
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white; 
            padding: 40px 30px; 
            text-align: center; 
        }
        .logo { 
            width: 60px; 
            height: 60px; 
            background: rgba(255, 255, 255, 0.2); 
            border-radius: 12px; 
            margin: 0 auto 20px; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
        }
        .header h1 { 
            font-size: 28px; 
            font-weight: 700; 
            margin-bottom: 8px; 
        }
        .header p { 
            font-size: 16px; 
            opacity: 0.9; 
        }
        .content { 
            padding: 40px 30px; 
            background: white; 
        }
        .content h2 { 
            color: #1f2937; 
            font-size: 24px; 
            font-weight: 600; 
            margin-bottom: 20px; 
        }
        .content p { 
            margin-bottom: 20px; 
            font-size: 16px; 
            line-height: 1.7; 
        }
        .button { 
            display: inline-block; 
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white; 
            padding: 16px 32px; 
            text-decoration: none; 
            border-radius: 8px; 
            font-weight: 600; 
            font-size: 16px;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .button:hover { 
            transform: translateY(-2px); 
        }
        .info-box { 
            background: #f3f4f6; 
            border-left: 4px solid #3b82f6; 
            padding: 20px; 
            margin: 20px 0; 
            border-radius: 0 8px 8px 0; 
        }
        .footer { 
            background: #f9fafb; 
            padding: 30px; 
            text-align: center; 
            border-top: 1px solid #e5e7eb; 
        }
        .footer p { 
            color: #6b7280; 
            font-size: 14px; 
            margin-bottom: 10px; 
        }
        .social-links { 
            margin-top: 20px; 
        }
        .social-links a { 
            display: inline-block; 
            margin: 0 10px; 
            color: #6b7280; 
            text-decoration: none; 
        }
        @media (max-width: 600px) {
            .email-container { margin: 0 10px; }
            .header, .content, .footer { padding: 20px; }
            .header h1 { font-size: 24px; }
            .content h2 { font-size: 20px; }
        }
    </style>
</head>
<body>
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
            <p><strong>ClassHero</strong> - Empowering learners worldwide</p>
            <p>This email was sent to you because you have an account with ClassHero.</p>
            <p>If you didn't request this email, you can safely ignore it.</p>
            
            <div class="social-links">
                <a href="#">Privacy Policy</a> | 
                <a href="#">Terms of Service</a> | 
                <a href="#">Contact Support</a>
            </div>
            
            <p style="margin-top: 20px; font-size: 12px; color: #9ca3af;">
                © {{ date('Y') }} ClassHero. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>