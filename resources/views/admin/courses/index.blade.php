@extends('layouts.admin')

@section('title', 'Course Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Course Management</h1>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mb-6">
        <form method="GET" class="flex items-center space-x-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search courses or teachers..." 
                   class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            <select name="status" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                <option value="">All Status</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Filter</button>
        </form>
    </div>

    <!-- Batch Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 mb-6">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <button onclick="showBatchModal()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg" id="batch-btn" disabled>
                    Batch Actions
                </button>
                <span id="selected-count" class="text-sm text-gray-600 dark:text-gray-400">0 selected</span>
            </div>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        @if($courses->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase hidden md:table-cell">Teacher</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase hidden lg:table-cell">Students</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase hidden lg:table-cell">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($courses as $course)
                            <tr>
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="course-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $course->id }}">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($course->getFirstMediaUrl('thumbnails'))
                                            <img src="{{ $course->getFirstMediaUrl('thumbnails') }}" alt="{{ $course->title }}" class="w-10 h-10 rounded-lg object-cover">
                                        @else
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                                <span class="text-white font-bold text-sm">{{ substr($course->title, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($course->title, 30) }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $course->category->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 md:hidden">by {{ $course->teacher->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white hidden md:table-cell">{{ $course->teacher->name }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $course->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $course->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $course->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($course->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white hidden lg:table-cell">{{ $course->enrolled_count }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 hidden lg:table-cell">{{ $course->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.courses.show', $course) }}" class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-2 py-1 rounded text-xs font-medium">
                                            View
                                        </a>
                                        <button onclick="deleteCourse({{ $course->id }})" class="bg-red-100 hover:bg-red-200 text-red-800 px-2 py-1 rounded text-xs font-medium">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4">{{ $courses->appends(request()->query())->links() }}</div>
        @else
            <div class="text-center py-12">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">No courses found</h3>
            </div>
        @endif
    </div>
</div>

<!-- Batch Action Modal -->
<div id="batchModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Batch Actions</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Action</label>
                    <select id="batchAction" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select Action</option>
                        <option value="publish">Publish Courses</option>
                        <option value="draft">Set to Draft</option>
                        <option value="pending">Set to Pending</option>
                        <option value="reject">Reject Courses</option>
                        <option value="delete">Delete Courses</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeBatchModal()" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                    Cancel
                </button>
                <button onclick="confirmBatchAction()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                    Execute
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="reject-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Reject Course</h3>
            <form id="reject-form" method="POST">
                @csrf
                <textarea name="reason" rows="4" placeholder="Reason for rejection..." required
                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" onclick="hideRejectModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Checkbox handling
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.course-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateBatchButton();
});

document.querySelectorAll('.course-checkbox').forEach(cb => {
    cb.addEventListener('change', updateBatchButton);
});

function updateBatchButton() {
    const checked = document.querySelectorAll('.course-checkbox:checked').length;
    document.getElementById('batch-btn').disabled = checked === 0;
    document.getElementById('selected-count').textContent = `${checked} selected`;
}

function changeStatus(courseId, status) {
    fetch(`/admin/courses/${courseId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
        } else {
            toastr.error(data.message);
        }
    });
}

function deleteCourse(courseId) {
    Swal.fire({
        title: 'Delete Course',
        text: 'Are you sure you want to delete this course?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Yes, delete!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/courses/${courseId}`, {
                method: 'DELETE',
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

function showBatchModal() {
    document.getElementById('batchModal').classList.remove('hidden');
}

function closeBatchModal() {
    document.getElementById('batchModal').classList.add('hidden');
    document.getElementById('batchAction').value = '';
}

function confirmBatchAction() {
    const action = document.getElementById('batchAction').value;
    
    if (!action) {
        toastr.error('Please select an action');
        return;
    }
    
    const selectedCourses = Array.from(document.querySelectorAll('.course-checkbox:checked')).map(cb => cb.value);
    
    fetch('/admin/courses/batch-action', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            action: action,
            courses: selectedCourses
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            location.reload();
        } else {
            toastr.error(data.message);
        }
    });
    
    closeBatchModal();
}

function showRejectModal(courseId) {
    document.getElementById('reject-form').action = `/admin/courses/${courseId}/reject`;
    document.getElementById('reject-modal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('reject-modal').classList.add('hidden');
}
</script>
@endsection