@extends('emails.layout')

@section('title', 'Verify Your Teacher Email')
@section('header-title', 'Teacher Verification')
@section('header-subtitle', 'Start your teaching journey with ClassHero')

@section('content')
<h2>Welcome, Educator! 👩‍🏫</h2>

<p>Hi <strong>{{ $user->name }}</strong>,</p>

<p>Thank you for joining ClassHero as a teacher! To start creating and sharing your knowledge, please verify your email address by clicking the button below.</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ $verificationUrl }}" class="button">
        📚 Verify Teacher Account
    </a>
</div>

<div class="info-box">
    <p><strong>🎓 Teaching Note:</strong> This verification link will expire in 60 minutes. Once verified, you can start creating courses immediately.</p>
</div>

<p>Once verified, you'll be able to:</p>
<ul style="margin: 20px 0; padding-left: 20px;">
    <li>📖 Create unlimited courses</li>
    <li>🎥 Upload video lessons</li>
    <li>📝 Build interactive quizzes</li>
    <li>👨‍🎓 Manage your students</li>
    <li>💰 Track your earnings</li>
</ul>

<p>If the button above doesn't work, copy and paste this link into your browser:</p>
<p style="word-break: break-all; background: #f3f4f6; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 14px;">
    {{ $verificationUrl }}
</p>

<p>If you didn't create a teacher account with ClassHero, you can safely ignore this email.</p>

<p>Happy teaching!<br>
<strong>The ClassHero Teaching Team</strong></p>
@endsection