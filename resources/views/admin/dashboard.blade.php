@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($stats['total_users']) }}
                    </p>

                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 mt-1 text-sm">
                        <span class="flex items-center gap-1 text-green-600 dark:text-green-400">
                            <span class="font-semibold">{{ number_format($stats['total_learners']) }}</span> Learners
                        </span>

                        <span class="hidden sm:inline w-px h-4 bg-gray-300 dark:bg-gray-600"></span>

                        <span class="flex items-center gap-1 text-blue-600 dark:text-blue-400">
                            <span class="font-semibold">{{ number_format($stats['total_teachers']) }}</span> Teachers
                        </span>

                        <span class="hidden sm:inline w-px h-4 bg-gray-300 dark:bg-gray-600"></span>

                        <span class="flex items-center gap-1 text-yellow-600 dark:text-yellow-400">
                            <span class="font-semibold">{{ number_format($stats['total_admins']) }}</span> Admins
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Courses</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_courses']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Enrollments</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_enrollments']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Revenue</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">${{ number_format($stats['total_revenue'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Online Users</p>
                <p class="text-xl font-semibold text-green-600 dark:text-green-400">{{ $stats['online_users'] }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Courses</p>
                <p class="text-xl font-semibold text-orange-600 dark:text-orange-400">{{ $stats['pending_courses'] }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Storage Used</p>
                <p class="text-xl font-semibold text-blue-600 dark:text-blue-400">{{ $stats['storage_used'] }} MB</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Monthly Revenue</p>
                <p class="text-xl font-semibold text-purple-600 dark:text-purple-400">${{ number_format($stats['monthly_revenue'], 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">User Growth</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Revenue Trend</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Users</h3>
            <div class="space-y-3">
                @foreach($recentUsers as $user)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-bold">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div class="ml-3">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 transition-colors">{{ $user->name }}</a>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full {{ $user->role === 'teacher' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Courses</h3>
            <div class="space-y-3">
                @foreach($recentCourses as $course)
                    <div class="flex items-center justify-between">
                        <div>
                            <a href="{{ route('admin.courses.show', $course->slug) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 transition-colors">{{ $course->title }}</a>
                            <p class="text-xs text-gray-500 dark:text-gray-400">by <a href="{{ route('admin.users.show', $course->teacher->id) }}" class="hover:text-blue-600 transition-colors">{{ $course->teacher->name }}</a></p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($course->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// User Growth Chart
const userCtx = document.getElementById('userGrowthChart').getContext('2d');
new Chart(userCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_map(function($i) { return now()->subMonths(11-$i)->format('M'); }, range(0, 11))) !!},
        datasets: [{
            label: 'New Users',
            data: {!! json_encode($monthlyUsers) !!},
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_map(function($i) { return now()->subMonths(11-$i)->format('M'); }, range(0, 11))) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode($monthlyRevenue) !!},
            backgroundColor: 'rgba(147, 51, 234, 0.8)',
            borderColor: 'rgb(147, 51, 234)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value;
                    }
                }
            }
        }
    }
});
</script>
@endpush
@endsection