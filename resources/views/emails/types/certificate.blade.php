@extends('emails.layout')

@section('title', 'Certificate Approved!')
@section('header-title', '🎉 Certificate Ready')
@section('header-subtitle', 'Your achievement is now certified')

@section('content')
<h2>Congratulations! Your Certificate is Ready</h2>

<div class="highlight-box">
    <p><strong>🏆 Course:</strong> {{ $data['course_title'] }}</p>
    <p><strong>📅 Issued:</strong> {{ date('F j, Y') }}</p>
</div>

<div style="text-align: center; margin: 32px 0;">
    <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 24px; border-radius: 16px; border: 2px solid #f59e0b;">
        <h3 style="color: #92400e; margin-bottom: 8px;">🎓 Achievement Unlocked</h3>
        <p style="color: #b45309;">You've successfully completed the course and earned your certificate!</p>
    </div>
</div>

<a href="{{ url('/certificates') }}" class="button">
    Download Certificate →
</a>

<p style="margin-top: 32px; color: #64748b;">
    Share your achievement and keep learning! 🚀
</p>
@endsection