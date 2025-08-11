<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = $this->getSettings();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'required|string|max:500',
            'contact_email' => 'required|email',
            'max_upload_size' => 'required|integer|min:1|max:1024',
            'allowed_file_types' => 'required|string',
            'course_approval_required' => 'boolean',
            'user_registration_enabled' => 'boolean',
            'email_verification_required' => 'boolean',
            'maintenance_mode' => 'boolean',
            'analytics_enabled' => 'boolean',
            'social_login_enabled' => 'boolean'
        ]);

        foreach ($request->except(['_token', '_method']) as $key => $value) {
            $this->setSetting($key, $value);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully!'
            ]);
        }

        return redirect()->route('admin.settings')
            ->with('success', 'Settings updated successfully!');
    }

    public function backup()
    {
        // Create database backup
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);
        
        // Ensure backup directory exists
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        // Simple backup command (adjust for your database)
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.host'),
            config('database.connections.mysql.database'),
            $path
        );

        exec($command);

        return response()->json([
            'success' => true,
            'message' => 'Database backup created successfully!',
            'filename' => $filename
        ]);
    }

    public function clearCache()
    {
        Cache::flush();
        \Artisan::call('config:clear');
        \Artisan::call('route:clear');
        \Artisan::call('view:clear');

        return response()->json([
            'success' => true,
            'message' => 'All caches cleared successfully!'
        ]);
    }

    private function getSettings()
    {
        return [
            'site_name' => $this->getSetting('site_name', config('app.name')),
            'site_description' => $this->getSetting('site_description', 'Modern E-Learning Platform'),
            'contact_email' => $this->getSetting('contact_email', 'admin@classhero.com'),
            'max_upload_size' => $this->getSetting('max_upload_size', 100),
            'allowed_file_types' => $this->getSetting('allowed_file_types', 'jpg,jpeg,png,gif,pdf,mp4,mp3'),
            'course_approval_required' => $this->getSetting('course_approval_required', true),
            'user_registration_enabled' => $this->getSetting('user_registration_enabled', true),
            'email_verification_required' => $this->getSetting('email_verification_required', true),
            'maintenance_mode' => $this->getSetting('maintenance_mode', false),
            'analytics_enabled' => $this->getSetting('analytics_enabled', true),
            'social_login_enabled' => $this->getSetting('social_login_enabled', false)
        ];
    }

    private function getSetting($key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function() use ($key, $default) {
            // In a real app, you'd store these in a settings table
            return $default;
        });
    }

    private function setSetting($key, $value)
    {
        // In a real app, you'd save to a settings table
        Cache::put("setting_{$key}", $value, 3600);
    }
}