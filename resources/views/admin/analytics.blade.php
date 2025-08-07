@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="bg-gradient-to-br from-purple-50 to-pink-100 dark:from-gray-900 dark:to-gray-800 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Analytics Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400">Comprehensive platform insights and metrics</p>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Enrollments Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Monthly Enrollments</h3>
                <canvas id="enrollmentsChart" width="400" height="200"></canvas>
            </div>

            <!-- Revenue Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Monthly Revenue</h3>
                <canvas id="revenueChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- User Growth Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">User Growth</h3>
            <canvas id="userGrowthChart" width="400" height="200"></canvas>
        </div>

        <!-- Category Performance -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Category Performance</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Category</th>
                            <th class="text-left py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Courses</th>
                            <th class="text-left py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Enrollments</th>
                            <th class="text-left py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($categoryStats as $category)
                            <tr>
                                <td class="py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $category['name'] }}</td>
                                <td class="py-3 text-sm text-gray-600 dark:text-gray-400">{{ $category['course_count'] }}</td>
                                <td class="py-3 text-sm text-gray-600 dark:text-gray-400">{{ number_format($category['total_enrollments']) }}</td>
                                <td class="py-3 text-sm text-gray-600 dark:text-gray-400">${{ number_format($category['total_revenue'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Top Courses -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Top Performing Courses</h3>
                <div class="space-y-4">
                    @foreach($topCourses as $course)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ Str::limit($course->title, 30) }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $course->teacher->name }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $course->enrolled_count }} students</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($course->rating, 1) }} ★</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Recent Enrollments</h3>
                <div class="space-y-4">
                    @foreach($recentEnrollments as $enrollment)
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-xs font-medium">{{ substr($enrollment->user->name, 0, 1) }}</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $enrollment->user->name }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">enrolled in {{ Str::limit($enrollment->course->title, 25) }}</p>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $enrollment->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Enrollments Chart
    const enrollmentsCtx = document.getElementById('enrollmentsChart').getContext('2d');
    new Chart(enrollmentsCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Enrollments',
                data: @json(array_values($monthlyEnrollments)),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
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
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Revenue',
                data: @json(array_values($monthlyRevenue)),
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
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

    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'New Users',
                data: @json(array_values($monthlyUsers)),
                borderColor: 'rgb(168, 85, 247)',
                backgroundColor: 'rgba(168, 85, 247, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
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
});
</script>
@endpush