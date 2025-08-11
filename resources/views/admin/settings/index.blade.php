@extends('layouts.admin')

@section('title', 'Platform Settings')
@section('page-title', 'Platform Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- General Settings -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">General Settings</h3>
        
        <form data-ajax data-success-message="Settings updated successfully!" action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="site_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Site Name</label>
                    <input type="text" id="site_name" name="site_name" value="{{ $settings['site_name'] }}" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Email</label>
                    <input type="email" id="contact_email" name="contact_email" value="{{ $settings['contact_email'] }}" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            <div>
                <label for="site_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Site Description</label>
                <textarea id="site_description" name="site_description" rows="3" required
                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">{{ $settings['site_description'] }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="max_upload_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Upload Size (MB)</label>
                    <input type="number" id="max_upload_size" name="max_upload_size" value="{{ $settings['max_upload_size'] }}" min="1" max="1024" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label for="allowed_file_types" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Allowed File Types</label>
                    <input type="text" id="allowed_file_types" name="allowed_file_types" value="{{ $settings['allowed_file_types'] }}" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                           placeholder="jpg,jpeg,png,gif,pdf,mp4,mp3">
                </div>
            </div>

            <!-- Feature Toggles -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Feature Settings</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-center">
                        <input type="checkbox" id="course_approval_required" name="course_approval_required" value="1" 
                               {{ $settings['course_approval_required'] ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="course_approval_required" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Course Approval Required
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="user_registration_enabled" name="user_registration_enabled" value="1" 
                               {{ $settings['user_registration_enabled'] ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="user_registration_enabled" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            User Registration Enabled
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="email_verification_required" name="email_verification_required" value="1" 
                               {{ $settings['email_verification_required'] ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="email_verification_required" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Email Verification Required
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" 
                               {{ $settings['maintenance_mode'] ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="maintenance_mode" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Maintenance Mode
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="analytics_enabled" name="analytics_enabled" value="1" 
                               {{ $settings['analytics_enabled'] ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="analytics_enabled" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Analytics Enabled
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="social_login_enabled" name="social_login_enabled" value="1" 
                               {{ $settings['social_login_enabled'] ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="social_login_enabled" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Social Login Enabled
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <x-spinning-button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    Save Settings
                </x-spinning-button>
            </div>
        </form>
    </div>

    <!-- System Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">System Actions</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button onclick="clearCache()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                Clear All Cache
            </button>
            
            <button onclick="createBackup()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                Create Database Backup
            </button>
            
            <button onclick="optimizeDatabase()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Optimize Database
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/forms.js') }}"></script>
<script>
function clearCache() {
    Swal.fire({
        title: 'Clear Cache',
        text: 'This will clear all application caches. Continue?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#7c3aed',
        confirmButtonText: 'Yes, clear cache!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/settings/clear-cache', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
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
    });
}

function createBackup() {
    Swal.fire({
        title: 'Create Backup',
        text: 'This will create a database backup. Continue?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        confirmButtonText: 'Yes, create backup!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/settings/backup', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
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
    });
}

function optimizeDatabase() {
    toastr.info('Database optimization feature coming soon!');
}
</script>
@endpush
@endsection