@extends('emails.layout')

@section('title', 'New Comment on Your Course')
@section('header-title', '💬 New Comment')
@section('header-subtitle', 'Someone commented on your course')

@section('content')
<h2>New Comment on Your Course</h2>

<div class="highlight-box">
    <p><strong>👤 Student:</strong> {{ $data['user_name'] }}</p>
    <p><strong>📚 Course:</strong> {{ $data['course_title'] ?? 'Your Course' }}</p>
</div>

<div style="background: #f8fafc; padding: 20px; border-radius: 12px; border-left: 4px solid #3b82f6; margin: 24px 0;">
    {!! $data['message'] !!}
</div>

<a href="{{ url('/teacher/courses') }}" class="button">
    View Course Comments →
</a>

<p style="margin-top: 32px; color: #64748b;">
    Stay engaged with your students! 🌟
</p>
@endsection