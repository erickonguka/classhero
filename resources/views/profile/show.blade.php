@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<!-- Page Loader -->
<div id="page-loader" class="fixed inset-0 bg-white dark:bg-gray-900 z-50 flex items-center justify-center">
    <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600 dark:text-gray-400">Loading profile...</p>
    </div>
</div>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-8">
            <div class="flex items-center space-x-6">
                <div class="relative">
                    @if($user->getProfilePictureUrl())
                        <img src="{{ $user->getProfilePictureUrl() }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full border-4 border-white object-cover">
                    @else
                        <div class="w-24 h-24 bg-white rounded-full border-4 border-white flex items-center justify-center">
                            <span class="text-2xl font-bold text-blue-600">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div class="text-white">
                    <h1 class="text-3xl font-bold">{{ $user->name }}</h1>
                    <p class="text-blue-100 capitalize">{{ $user->role }}</p>
                    @if($user->bio)
                        <p class="text-blue-100 mt-2">{{ $user->bio }}</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Profile Information</h2>
                <a href="{{ route('profile.edit') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Edit Profile
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <p class="text-gray-900 dark:text-white">{{ $user->email }}</p>
                </div>
                @if($user->country_code)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Country</label>
                    <p class="text-gray-900 dark:text-white">{{ $user->getCountryName() }}</p>
                </div>
                @endif
                @if($user->phone)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                    <p class="text-gray-900 dark:text-white">{{ $user->phone }}</p>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                    <p class="text-gray-900 dark:text-white capitalize">{{ $user->role }}</p>
                </div>
                @if($user->currency)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Currency</label>
                    <p class="text-gray-900 dark:text-white">{{ $user->currency }} ({{ $user->getCurrencySymbol() }})</p>
                </div>
                @endif
                @if($user->points)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Points</label>
                    <p class="text-gray-900 dark:text-white">{{ number_format($user->points) }}</p>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Member Since</label>
                    <p class="text-gray-900 dark:text-white">{{ $user->created_at->format('F j, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection