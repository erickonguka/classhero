@extends('emails.layout')

@section('title', $data['title'])
@section('header-title', $data['title'])
@section('header-subtitle', 'ClassHero Notification')

@section('content')
<h2>{{ $data['title'] }}</h2>

<p>{!! nl2br(e($data['message'])) !!}</p>

@if(isset($data['sender']))
<div class="info-box">
    <p><strong>📧 From:</strong> {{ $data['sender'] }}</p>
</div>
@endif

<p>Thank you for being part of the ClassHero community!</p>

<p>Best regards,<br>
<strong>The ClassHero Team</strong></p>
@endsection