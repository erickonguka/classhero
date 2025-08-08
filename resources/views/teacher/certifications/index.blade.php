@extends('layouts.teacher')

@section('title', 'Certification Approvals')
@section('page-title', 'Certification Approvals')

@section('content')
<div class="p-6">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-gray-600 dark:text-gray-400">Review and approve student course completions for certification</p>
    </div>

    <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search students..." 
                   class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            <select name="course_id" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                <option value="">All Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->title }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Filter</button>
        </form>
    </div>

    @if($completedEnrollments->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pending Certifications</h2>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($completedEnrollments as $enrollment)
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700" id="enrollment-{{ $enrollment->id }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                @if($enrollment->user->getProfilePictureUrl())
                                    <img src="{{ $enrollment->user->getProfilePictureUrl() }}" alt="{{ $enrollment->user->name }}" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold">{{ substr($enrollment->user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $enrollment->user->name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $enrollment->user->email }}</p>
                                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ $enrollment->course->title }}</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Progress</div>
                                    <div class="text-lg font-bold text-green-600 dark:text-green-400">{{ $enrollment->progress_percentage }}%</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $enrollment->lessons_completed }}/{{ $enrollment->total_lessons }} lessons
                                    </div>
                                </div>

                                <div class="flex space-x-2">
                                    <button onclick="approveCertification({{ $enrollment->id }})" 
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        Approve
                                    </button>
                                    <button onclick="showRejectModal({{ $enrollment->id }})" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        Reject
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Enrolled:</span>
                                <span class="text-gray-900 dark:text-white ml-1">{{ $enrollment->enrolled_at->format('M d, Y') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Course Duration:</span>
                                <span class="text-gray-900 dark:text-white ml-1">{{ $enrollment->course->duration_hours }}h</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Course Price:</span>
                                <span class="text-gray-900 dark:text-white ml-1">${{ number_format($enrollment->course->price ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $completedEnrollments->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No pending certifications</h3>
            <p class="text-gray-600 dark:text-gray-400">Student completion approvals will appear here</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
        @if($rejectedEnrollments->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recently Rejected</h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
                    @foreach($rejectedEnrollments as $enrollment)
                        <div class="p-4 bg-red-50 dark:bg-red-900/20">
                            <div class="flex items-center space-x-3">
                                @if($enrollment->user->getProfilePictureUrl())
                                    <img src="{{ $enrollment->user->getProfilePictureUrl() }}" alt="{{ $enrollment->user->name }}" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 bg-gradient-to-r from-red-500 to-pink-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-bold">{{ substr($enrollment->user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $enrollment->user->name }}</h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $enrollment->course->title }}</p>
                                    <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $enrollment->rejection_reason }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $enrollment->rejected_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($approvedEnrollments->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Approved Certifications</h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
                    @foreach($approvedEnrollments as $enrollment)
                        <div class="p-4 bg-green-50 dark:bg-green-900/20">
                            <div class="flex items-center space-x-3">
                                @if($enrollment->user->getProfilePictureUrl())
                                    <img src="{{ $enrollment->user->getProfilePictureUrl() }}" alt="{{ $enrollment->user->name }}" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-bold">{{ substr($enrollment->user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $enrollment->user->name }}</h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $enrollment->course->title }}</p>
                                    <p class="text-xs text-green-600 dark:text-green-400">Certificate Generated</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $enrollment->completed_at->format('M d, Y') }}</p>
                                </div>
                                @if($enrollment->certificateRelation)
                                    <a href="{{ route('certificate.show', $enrollment->certificateRelation) }}" target="_blank" 
                                       class="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-xs">
                                        View Certificate
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Reject Certification</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Please provide a reason for rejecting this certification:</p>
            
            <textarea id="rejectionReason" rows="4" 
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                      placeholder="Explain why the certification is being rejected..."></textarea>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeRejectModal()" 
                        class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                    Cancel
                </button>
                <button onclick="confirmReject()" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">
                    Reject Certification
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentEnrollmentId = null;

function approveCertification(enrollmentId) {
    Swal.fire({
        title: 'Approve Certification',
        text: 'Are you sure you want to approve this certification? This will generate a certificate for the student.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, approve it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/teacher/certifications/${enrollmentId}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`enrollment-${enrollmentId}`).remove();
                    toastr.success(data.message);
                    
                    // Check if no more enrollments
                    if (document.querySelectorAll('[id^="enrollment-"]').length === 0) {
                        location.reload();
                    }
                } else {
                    toastr.error(data.message || 'Failed to approve certification');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('An error occurred while approving the certification');
            });
        }
    });
}

function showRejectModal(enrollmentId) {
    currentEnrollmentId = enrollmentId;
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectionReason').value = '';
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    currentEnrollmentId = null;
}

function confirmReject() {
    const reason = document.getElementById('rejectionReason').value.trim();
    
    if (!reason) {
        toastr.error('Please provide a reason for rejection');
        return;
    }

    fetch(`/teacher/certifications/${currentEnrollmentId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`enrollment-${currentEnrollmentId}`).remove();
            closeRejectModal();
            toastr.success(data.message);
            
            // Check if no more enrollments
            if (document.querySelectorAll('[id^="enrollment-"]').length === 0) {
                location.reload();
            }
        } else {
            toastr.error(data.message || 'Failed to reject certification');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('An error occurred while rejecting the certification');
    });
}
</script>
@endsection