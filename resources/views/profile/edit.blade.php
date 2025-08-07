@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="bg-gradient-to-br from-indigo-50 to-purple-100 dark:from-gray-900 dark:to-gray-800 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Profile Settings</h1>
            <p class="text-gray-600 dark:text-gray-400">Manage your account information and preferences</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            @if($user->role === 'learner')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['courses_enrolled'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Courses Enrolled</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['courses_completed'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Completed</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['total_points'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Points Earned</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['certificates'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Certificates</div>
                </div>
            @elseif($user->role === 'teacher')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['courses_created'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Courses Created</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['total_students'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Students</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">${{ number_format($stats['total_revenue'], 2) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Revenue</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($stats['avg_rating'], 1) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Avg Rating</div>
                </div>
            @elseif($user->role === 'admin')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['total_users'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Users</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['total_courses'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Courses</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">${{ number_format($stats['total_revenue'], 2) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Platform Revenue</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['platform_growth'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">New Users This Month</div>
                </div>
            @endif
        </div>

        <!-- Profile Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Profile Information</h2>
            
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                @csrf
                @method('patch')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bio</label>
                    <textarea id="bio" name="bio" rows="4"
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                              placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between pt-6">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Role: <span class="font-medium capitalize">{{ $user->role }}</span>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>

        <!-- Delete Account -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 mt-8">
            <h2 class="text-xl font-bold text-red-600 dark:text-red-400 mb-4">Delete Account</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Once your account is deleted, all of its resources and data will be permanently deleted.
            </p>
            <button onclick="confirmDelete()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                Delete Account
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Confirm Account Deletion</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
            Please enter your password to confirm you would like to permanently delete your account.
        </p>
        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')
            <input type="password" name="password" placeholder="Password" required
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white mb-4">
            <div class="flex items-center justify-end space-x-4">
                <button type="button" onclick="closeDeleteModal()" class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                    Cancel
                </button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Delete Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
}
</script>
@endpush