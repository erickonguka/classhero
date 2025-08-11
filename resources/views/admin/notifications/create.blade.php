@extends('layouts.admin')

@section('title', 'Send Notification')
@section('page-title', 'Send Notification')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <form data-ajax data-success-message="Notification sent successfully!" action="{{ route('admin.notifications.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title *</label>
                    <input type="text" id="title" name="title" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                           placeholder="Notification title">
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type *</label>
                    <select id="type" name="type" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="announcement">Announcement</option>
                        <option value="alert">Alert</option>
                        <option value="info">Information</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message *</label>
                <textarea id="message" name="message" rows="8"></textarea>
                <div id="message-error" class="text-red-500 text-sm mt-1 hidden">Message is required</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="recipients" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Recipients *</label>
                    <select id="recipients" name="recipients" required onchange="toggleUserSelect()"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="all">👥 All Users</option>
                        <option value="teachers">👩‍🏫 Teachers Only</option>
                        <option value="learners">🎓 Learners Only</option>
                        <option value="admins">🛡️ Admins Only</option>
                        <option value="new_users">✨ New Users (Last 7 days)</option>
                        <option value="specific">👤 Specific User</option>
                    </select>
                </div>
                
                <div id="userSelect" class="hidden">
                    <label for="user_search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search User</label>
                    <div class="relative">
                        <input type="text" id="user_search" placeholder="Type name or email to search..." 
                               class="w-full px-4 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                               autocomplete="off">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="hidden" id="user_id" name="user_id">
                        <div id="user_results" class="absolute z-20 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-lg hidden"></div>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="send_email" name="send_email" value="1"
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="send_email" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                        Also send via email
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.notifications.index') }}" 
                   class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </a>
                <x-spinning-button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    Send Notification
                </x-spinning-button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
<script>
tinymce.init({
    selector: '#message',
    height: 300,
    menubar: false,
    plugins: 'lists link code',
    toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link | code',
    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px }',
    setup: function(editor) {
        editor.on('change', function() {
            document.getElementById('message-error').classList.add('hidden');
        });
    }
});

// Custom form validation
document.querySelector('form[data-ajax]').addEventListener('submit', function(e) {
    // Force TinyMCE to save content to textarea
    tinymce.get('message').save();
    
    const content = tinymce.get('message').getContent().trim();
    if (!content || content === '<p></p>' || content === '<p><br></p>') {
        e.preventDefault();
        document.getElementById('message-error').classList.remove('hidden');
        tinymce.get('message').focus();
        return false;
    }
    
    // Hide error if content is valid
    document.getElementById('message-error').classList.add('hidden');
});

function toggleUserSelect() {
    const recipients = document.getElementById('recipients').value;
    const userSelect = document.getElementById('userSelect');
    const userIdField = document.getElementById('user_id');
    const userSearch = document.getElementById('user_search');
    
    if (recipients === 'specific') {
        userSelect.classList.remove('hidden');
        userIdField.required = true;
        userSearch.required = true;
    } else {
        userSelect.classList.add('hidden');
        userIdField.required = false;
        userSearch.required = false;
        userIdField.value = '';
        userSearch.value = '';
    }
}

// User search functionality with debouncing
let searchTimeout;
document.getElementById('user_search').addEventListener('input', function() {
    const query = this.value.trim();
    const results = document.getElementById('user_results');
    
    // Clear previous timeout
    clearTimeout(searchTimeout);
    
    if (query.length < 2) {
        results.classList.add('hidden');
        document.getElementById('user_id').value = '';
        return;
    }
    
    // Show loading state
    results.innerHTML = '<div class="p-3 text-gray-500 dark:text-gray-400 flex items-center"><svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Searching...</div>';
    results.classList.remove('hidden');
    
    // Debounce search requests
    searchTimeout = setTimeout(() => {
        fetch(`/admin/users/search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(users => {
                results.innerHTML = '';
                if (users.length > 0) {
                    users.forEach((user, index) => {
                        const div = document.createElement('div');
                        div.className = 'p-3 hover:bg-blue-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-150 ' + (index === users.length - 1 ? '' : 'border-b border-gray-200 dark:border-gray-600');
                        
                        // Highlight matching text
                        const nameHighlighted = highlightMatch(user.name, query);
                        const emailHighlighted = highlightMatch(user.email, query);
                        
                        div.innerHTML = `
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900 dark:text-white">${nameHighlighted}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">${emailHighlighted}</div>
                                </div>
                                <div class="ml-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${
                                        user.role === 'admin' ? 'bg-purple-100 text-purple-800' :
                                        user.role === 'teacher' ? 'bg-green-100 text-green-800' :
                                        'bg-blue-100 text-blue-800'
                                    }">
                                        ${user.role.charAt(0).toUpperCase() + user.role.slice(1)}
                                    </span>
                                </div>
                            </div>
                        `;
                        div.addEventListener('click', () => {
                            document.getElementById('user_search').value = `${user.name} (${user.email})`;
                            document.getElementById('user_id').value = user.id;
                            results.classList.add('hidden');
                        });
                        results.appendChild(div);
                    });
                    results.classList.remove('hidden');
                } else {
                    results.innerHTML = '<div class="p-3 text-center text-gray-500 dark:text-gray-400"><svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>No users found</div>';
                    results.classList.remove('hidden');
                }
            })
            .catch(error => {
                results.innerHTML = '<div class="p-3 text-center text-red-500 dark:text-red-400">Error searching users</div>';
                results.classList.remove('hidden');
            });
    }, 300);
});

// Function to highlight matching text
function highlightMatch(text, query) {
    const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    return text.replace(regex, '<mark class="bg-yellow-200 dark:bg-yellow-600 px-1 rounded">$1</mark>');
}

// Hide results when clicking outside
document.addEventListener('click', function(e) {
    const userSelect = document.getElementById('userSelect');
    if (userSelect && !userSelect.contains(e.target)) {
        document.getElementById('user_results').classList.add('hidden');
    }
});

// Handle keyboard navigation
document.getElementById('user_search').addEventListener('keydown', function(e) {
    const results = document.getElementById('user_results');
    const items = results.querySelectorAll('[data-user-item]');
    
    if (e.key === 'Escape') {
        results.classList.add('hidden');
        this.blur();
    }
});
</script>
<script src="{{ asset('js/forms.js') }}"></script>
@endpush
@endsection