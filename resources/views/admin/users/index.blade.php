@extends('layouts.admin')

@section('title', 'Users Management')
@section('page-title', 'Users Management')

@section('content')
<div class="space-y-6">
    <!-- Filters and Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search users..." 
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                
                <select name="role" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                    <option value="learner" {{ request('role') === 'learner' ? 'selected' : '' }}>Learner</option>
                </select>

                <select name="status" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Banned</option>
                </select>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Filter
                </button>
            </form>

            <div class="flex gap-2">
                <button onclick="showBatchModal()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed" id="batch-btn" disabled>
                    Batch Actions (<span id="selected-count">0</span>)
                </button>
                <a href="{{ route('admin.users.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    Add User
                </a>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left">
                            <a href="?{{ http_build_query(array_merge(request()->all(), ['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" 
                               class="text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:text-gray-700 dark:hover:text-gray-100">
                                User
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <a href="?{{ http_build_query(array_merge(request()->all(), ['sort' => 'role', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" 
                               class="text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:text-gray-700 dark:hover:text-gray-100">
                                Role
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left">
                            <a href="?{{ http_build_query(array_merge(request()->all(), ['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" 
                               class="text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:text-gray-700 dark:hover:text-gray-100">
                                Joined
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="user-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $user->id }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($user->getProfilePictureUrl())
                                        <img src="{{ $user->getProfilePictureUrl() }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover">
                                    @else
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-white font-bold">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 transition-colors">{{ $user->name }}</a>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                        @if($user->country_code)
                                            <div class="text-xs text-gray-400 flex items-center mt-1">
                                                <span class="mr-1">{{ $user->getCountryFlag() }}</span>
                                                {{ $user->getCountryName() }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : ($user->role === 'teacher' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->banned_at)
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Banned</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">View</a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Edit</a>
                                    @if($user->id !== auth()->id())
                                        <button onclick="loginAs({{ $user->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400">Login As</button>
                                        @if($user->banned_at)
                                            <button onclick="unbanUser({{ $user->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400">Unban</button>
                                        @else
                                            <button onclick="banUser({{ $user->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400">Ban</button>
                                        @endif
                                        <button onclick="deleteUser({{ $user->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400">Delete</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Ban Modal -->
<div id="banModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ban User</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Please provide a reason for banning this user:</p>
            
            <textarea id="banReason" rows="4" 
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                      placeholder="Reason for ban..."></textarea>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeBanModal()" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                    Cancel
                </button>
                <button onclick="confirmBan()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">
                    Ban User
                </button>
            </div>
        </div>
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
                        <option value="ban">Ban Users</option>
                        <option value="unban">Unban Users</option>
                        <option value="delete">Delete Users</option>
                    </select>
                </div>
                
                <div id="batchReasonDiv" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason</label>
                    <textarea id="batchReason" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                              placeholder="Reason for action..."></textarea>
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

@push('scripts')
<script>
let currentUserId = null;

// Checkbox handling
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateBatchButton();
});

document.querySelectorAll('.user-checkbox').forEach(cb => {
    cb.addEventListener('change', updateBatchButton);
});

function updateBatchButton() {
    const checked = document.querySelectorAll('.user-checkbox:checked').length;
    const batchBtn = document.getElementById('batch-btn');
    const countSpan = document.getElementById('selected-count');
    
    batchBtn.disabled = checked === 0;
    countSpan.textContent = checked;
}

// Ban user
function banUser(userId) {
    currentUserId = userId;
    document.getElementById('banModal').classList.remove('hidden');
}

function closeBanModal() {
    document.getElementById('banModal').classList.add('hidden');
    document.getElementById('banReason').value = '';
    currentUserId = null;
}

function confirmBan() {
    const reason = document.getElementById('banReason').value.trim();
    if (!reason) {
        toastr.error('Please provide a reason for banning');
        return;
    }

    fetch(`/admin/users/${currentUserId}/ban`, {
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
            toastr.success(data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            toastr.error(data.message);
        }
    })
    .catch(error => {
        toastr.error('An error occurred');
    });

    closeBanModal();
}

// Unban user
function unbanUser(userId) {
    Swal.fire({
        title: 'Unban User',
        text: 'Are you sure you want to unban this user?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, unban!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/users/${userId}/unban`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(data.message);
                }
            });
        }
    });
}

// Delete user
function deleteUser(userId) {
    Swal.fire({
        title: 'Delete User',
        text: 'Are you sure you want to delete this user? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/users/${userId}`, {
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
                } else {
                    toastr.error(data.message);
                }
            });
        }
    });
}

// Login as user
function loginAs(userId) {
    Swal.fire({
        title: 'Login as User',
        text: 'You will be logged in as this user. Continue?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, login!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `/admin/users/${userId}/login-as`;
        }
    });
}

// Batch actions
function showBatchModal() {
    document.getElementById('batchModal').classList.remove('hidden');
}

function closeBatchModal() {
    document.getElementById('batchModal').classList.add('hidden');
    document.getElementById('batchAction').value = '';
    document.getElementById('batchReason').value = '';
    document.getElementById('batchReasonDiv').classList.add('hidden');
}

document.getElementById('batchAction').addEventListener('change', function() {
    const reasonDiv = document.getElementById('batchReasonDiv');
    if (this.value === 'ban') {
        reasonDiv.classList.remove('hidden');
    } else {
        reasonDiv.classList.add('hidden');
    }
});

function confirmBatchAction() {
    const action = document.getElementById('batchAction').value;
    const reason = document.getElementById('batchReason').value.trim();
    
    if (!action) {
        toastr.error('Please select an action');
        return;
    }
    
    if (action === 'ban' && !reason) {
        toastr.error('Please provide a reason for banning');
        return;
    }
    
    const selectedUsers = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    
    const requestData = {
        action: action,
        users: selectedUsers
    };
    
    if (action === 'ban' && reason) {
        requestData.reason = reason;
    }
    
    fetch('/admin/users/batch-action', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(`HTTP ${response.status}: Server returned HTML instead of JSON`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            location.reload();
        } else {
            toastr.error(data.message || 'Operation failed');
        }
    })
    .catch(error => {
        console.error('Batch action error:', error);
        if (error.message.includes('Unexpected token')) {
            toastr.error('Server error occurred. Please check the logs.');
        } else {
            toastr.error('An error occurred: ' + error.message);
        }
    });
    
    closeBatchModal();
}
</script>
@endpush
@endsection