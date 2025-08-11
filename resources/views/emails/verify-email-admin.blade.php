@extends('emails.layout')

@section('title', 'Verify Your Admin Email')
@section('header-title', 'Admin Verification')
@section('header-subtitle', 'Secure your ClassHero admin account')

@section('content')
<h2>Welcome, Administrator! 🛡️</h2>

<p>Hi <strong>{{ $user->name }}</strong>,</p>

<p>Thank you for joining ClassHero as an administrator! To secure your admin account and complete your registration, please verify your email address by clicking the button below.</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ $verificationUrl }}" class="button">
        🔐 Verify Admin Account
    </a>
</div>

<div class="info-box">
    <p><strong>🔒 Security Note:</strong> This verification link will expire in 60 minutes. As an admin, your account requires additional security measures.</p>
</div>

<p>Once verified, you'll have access to:</p>
<ul style="margin: 20px 0; padding-left: 20px;">
    <li>🏛️ Complete admin dashboard</li>
    <li>👥 User management system</li>
    <li>📊 Platform analytics</li>
    <li>🔧 System settings</li>
    <li>📢 Notification management</li>
</ul>

<p>If the button above doesn't work, copy and paste this link into your browser:</p>
<p style="word-break: break-all; background: #f3f4f6; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 14px;">
    {{ $verificationUrl }}
</p>

<p>If you didn't create an admin account with ClassHero, please contact our security team immediately.</p>

<p>Best regards,<br>
<strong>The ClassHero Security Team</strong></p>
@endsection