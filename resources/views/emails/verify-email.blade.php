@extends('emails.layout')

@section('title', 'Verify Your Email Address')
@section('header-title', 'Verify Your Email')
@section('header-subtitle', 'Complete your ClassHero registration')

@section('content')
<h2>Welcome to ClassHero! 🎓</h2>

<p>Hi <strong>{{ $user->name }}</strong>,</p>

<p>Thank you for joining ClassHero! To complete your registration and start your learning journey, please verify your email address by clicking the button below.</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ $verificationUrl }}" class="button">
        ✅ Verify Email Address
    </a>
</div>

<div class="info-box">
    <p><strong>🔒 Security Note:</strong> This verification link will expire in 60 minutes for your security.</p>
</div>

<p>Once verified, you'll be able to:</p>
<ul style="margin: 20px 0; padding-left: 20px;">
    <li>🎯 Access thousands of courses</li>
    <li>📊 Track your learning progress</li>
    <li>🏆 Earn certificates</li>
    <li>💬 Join course discussions</li>
    <li>⭐ Rate and review courses</li>
</ul>

<p>If the button above doesn't work, you can copy and paste this link into your browser:</p>
<p style="word-break: break-all; background: #f3f4f6; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 14px;">
    {{ $verificationUrl }}
</p>

<p>If you didn't create an account with ClassHero, you can safely ignore this email.</p>

<p>Happy learning!<br>
<strong>The ClassHero Team</strong></p>
@endsection