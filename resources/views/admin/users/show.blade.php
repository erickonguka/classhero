@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">User Details</h1>
        <a href="{{ route('admin.users.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
            Back to Users
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Info -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="text-center">
                    @if($user->getProfilePictureUrl())
                        <img src="{{ $user->getProfilePictureUrl() }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full mx-auto object-cover">
                    @else
                        <div class="w-24 h-24 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto">
                            <span class="text-white font-bold text-2xl">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mt-4">{{ $user->name }}</h2>
                    <p class="text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                    <span class="inline-block px-3 py-1 text-sm rounded-full mt-2 {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : ($user->role === 'teacher' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>

                <div class="mt-6 space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                        <p class="text-gray-900 dark:text-white">
                            @if($user->banned_at)
                                <span class="text-red-600">Banned</span>
                                <small class="block text-gray-500">{{ $user->banned_at->format('M d, Y') }}</small>
                            @else
                                <span class="text-green-600">Active</span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Joined</label>
                        <p class="text-gray-900 dark:text-white">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>

                    @if($user->country_code)
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Country</label>
                        <p class="text-gray-900 dark:text-white">{{ $user->getCountryFlag() }} {{ $user->getCountryName() }}</p>
                    </div>
                    @endif

                    @if($user->points)
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Points</label>
                        <p class="text-gray-900 dark:text-white">{{ number_format($user->points) }}</p>
                    </div>
                    @endif
                </div>

                @if($user->id !== auth()->id())
                <div class="mt-6 space-y-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center block">
                        Edit User
                    </a>
                    @if($user->banned_at)
                        <button onclick="unbanUser({{ $user->id }})" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                            Unban User
                        </button>
                    @else
                        <button onclick="banUser({{ $user->id }})" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                            Ban User
                        </button>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Activity & Stats -->
        <div class="lg:col-span-2 space-y-6">
            @if($user->role === 'teacher')
            <!-- Teacher Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Teaching Statistics</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $user->courses->count() }}</div>
                        <div class="text-sm text-gray-500">Courses</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $user->courses->sum('enrolled_count') }}</div>
                        <div class="text-sm text-gray-500">Students</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ number_format($user->courses->avg('rating') ?? 0, 1) }}</div>
                        <div class="text-sm text-gray-500">Avg Rating</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $user->payments->sum('amount') }}</div>
                        <div class="text-sm text-gray-500">Revenue</div>
                    </div>
                </div>
            </div>

            <!-- Courses -->
            @if($user->courses->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Courses</h3>
                <div class="space-y-3">
                    @foreach($user->courses->take(5) as $course)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">{{ $course->title }}</h4>
                            <p class="text-sm text-gray-500">{{ $course->enrolled_count }} students</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($course->status) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @endif

            @if($user->role === 'learner')
            <!-- Learner Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Learning Statistics</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $user->enrollments->count() }}</div>
                        <div class="text-sm text-gray-500">Enrolled</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $user->certificates->count() }}</div>
                        <div class="text-sm text-gray-500">Certificates</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $user->points ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Points</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $user->payments->sum('amount') }}</div>
                        <div class="text-sm text-gray-500">Spent</div>
                    </div>
                </div>
            </div>

            <!-- Enrollments -->
            @if($user->enrollments->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Enrollments</h3>
                <div class="space-y-3">
                    @foreach($user->enrollments->take(5) as $enrollment)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">{{ $enrollment->course->title }}</h4>
                            <p class="text-sm text-gray-500">Enrolled {{ $enrollment->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $enrollment->progress }}%</div>
                            <div class="w-16 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $enrollment->progress }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @endif

            <!-- Recent Payments -->
            @if($user->payments->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Payments</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-2 text-sm font-medium text-gray-500">Course</th>
                                <th class="text-left py-2 text-sm font-medium text-gray-500">Amount</th>
                                <th class="text-left py-2 text-sm font-medium text-gray-500">Date</th>
                                <th class="text-left py-2 text-sm font-medium text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->payments->take(5) as $payment)
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-2 text-sm text-gray-900 dark:text-white">{{ $payment->course->title }}</td>
                                <td class="py-2 text-sm text-gray-900 dark:text-white">${{ number_format($payment->amount, 2) }}</td>
                                <td class="py-2 text-sm text-gray-500">{{ $payment->created_at->format('M d, Y') }}</td>
                                <td class="py-2">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function banUser(userId) {
    Swal.fire({
        title: 'Ban User',
        input: 'textarea',
        inputLabel: 'Reason for banning',
        inputPlaceholder: 'Enter reason...',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Ban User'
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            fetch(`/admin/users/${userId}/ban`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ reason: result.value })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message);
                    location.reload();
                }
            });
        }
    });
}

function unbanUser(userId) {
    Swal.fire({
        title: 'Unban User',
        text: 'Are you sure you want to unban this user?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        confirmButtonText: 'Yes, unban!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/users/${userId}/unban`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message);
                    location.reload();
                }
            });
        }
    });
}
</script>
@endpush
@endsection