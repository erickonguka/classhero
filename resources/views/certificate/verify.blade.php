@extends('layouts.app')

@section('title', 'Certificate Verification')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 text-center">
        @if($certificate)
            <div class="mb-6">
                <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h1 class="text-3xl font-bold text-green-600 mb-2">Certificate Verified</h1>
                <p class="text-gray-600 dark:text-gray-400">This certificate is authentic and valid.</p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Certificate Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Student Name</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $certificate->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Course Title</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $certificate->course->title }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Completion Date</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $certificate->created_at->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Certificate ID</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $certificate->verification_code }}</p>
                    </div>
                </div>
            </div>

            <a href="{{ route('certificate.show', $certificate) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                View Certificate
            </a>
        @else
            <div class="mb-6">
                <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h1 class="text-3xl font-bold text-red-600 mb-2">Certificate Not Found</h1>
                <p class="text-gray-600 dark:text-gray-400">The certificate with this verification code could not be found or is invalid.</p>
            </div>
        @endif
    </div>
</div>
@endsection