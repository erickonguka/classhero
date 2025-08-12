@extends('emails.layout')

@section('title', $data['title'])
@section('header-title', $data['title'])
@section('header-subtitle', 'ClassHero Notification')

@section('content')
<h2>{{ $data['title'] }}</h2>

<div style="background: #f8fafc; padding: 24px; border-radius: 12px; border-left: 4px solid #3b82f6; margin: 24px 0; line-height: 1.7;">
    {!! $data['message'] !!}
</div>

@if(isset($data['sender']))
<div class="highlight-box">
    <p><strong>💬 From:</strong> {{ $data['sender'] }}</p>
    @if(isset($data['sender_role']))
        <p><strong>Role:</strong> {{ $data['sender_role'] }}</p>
    @endif
</div>
@endif

@if(isset($data['course_title']))
<div class="highlight-box">
    <p><strong>📚 Course:</strong> {{ $data['course_title'] }}</p>
</div>
@endif

@if(isset($data['rating']))
<div class="stats-grid">
    <div class="stat-item">
        <div class="stat-number">{{ $data['rating'] }}</div>
        <div class="stat-label">Stars</div>
    </div>
</div>
@endif

<div style="text-align: center; margin: 32px 0;">
    <a href="{{ url('/dashboard') }}" class="button">
        View in Dashboard →
    </a>
</div>

<p style="margin-top: 32px; color: #64748b; text-align: center;">
    Thank you for being part of the ClassHero community! 🎆
</p>

<p style="color: #64748b; text-align: center;">
    Best regards,<br>
    <strong style="color: #1e40af;">The ClassHero Team</strong>
</p>
@endsection