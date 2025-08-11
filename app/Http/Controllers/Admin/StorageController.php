<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class StorageController extends Controller
{
    public function index()
    {
        $storageData = $this->getStorageData();
        return view('admin.storage.index', compact('storageData'));
    }

    public function cleanup(Request $request)
    {
        $request->validate([
            'action' => 'required|in:temp_files,old_logs,unused_media,cache'
        ]);

        $cleaned = 0;
        $message = '';

        switch ($request->action) {
            case 'temp_files':
                $cleaned = $this->cleanTempFiles();
                $message = "Cleaned {$cleaned} temporary files";
                break;
            case 'old_logs':
                $cleaned = $this->cleanOldLogs();
                $message = "Cleaned {$cleaned} old log files";
                break;
            case 'unused_media':
                $cleaned = $this->cleanUnusedMedia();
                $message = "Cleaned {$cleaned} unused media files";
                break;
            case 'cache':
                $this->clearCache();
                $message = "Cache cleared successfully";
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    private function getStorageData()
    {
        $publicPath = storage_path('app/public');
        $logsPath = storage_path('logs');
        $cachePath = storage_path('framework/cache');
        
        return [
            'total_size' => $this->getDirectorySize(storage_path('app')),
            'public_storage' => [
                'size' => $this->getDirectorySize($publicPath),
                'files' => $this->countFiles($publicPath)
            ],
            'logs' => [
                'size' => $this->getDirectorySize($logsPath),
                'files' => $this->countFiles($logsPath)
            ],
            'cache' => [
                'size' => $this->getDirectorySize($cachePath),
                'files' => $this->countFiles($cachePath)
            ],
            'temp_files' => [
                'size' => $this->getTempFilesSize(),
                'files' => $this->countTempFiles()
            ],
            'disk_usage' => $this->getDiskUsage()
        ];
    }

    private function getDirectorySize($path)
    {
        if (!is_dir($path)) return 0;
        
        $size = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            $size += $file->getSize();
        }
        
        return $size;
    }

    private function countFiles($path)
    {
        if (!is_dir($path)) return 0;
        
        $count = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) $count++;
        }
        
        return $count;
    }

    private function getTempFilesSize()
    {
        $tempPaths = [
            storage_path('app/temp'),
            sys_get_temp_dir() . '/laravel*'
        ];
        
        $size = 0;
        foreach ($tempPaths as $path) {
            if (is_dir($path)) {
                $size += $this->getDirectorySize($path);
            }
        }
        
        return $size;
    }

    private function countTempFiles()
    {
        $tempPaths = [
            storage_path('app/temp'),
            sys_get_temp_dir() . '/laravel*'
        ];
        
        $count = 0;
        foreach ($tempPaths as $path) {
            if (is_dir($path)) {
                $count += $this->countFiles($path);
            }
        }
        
        return $count;
    }

    private function getDiskUsage()
    {
        $total = disk_total_space(storage_path());
        $free = disk_free_space(storage_path());
        $used = $total - $free;
        
        return [
            'total' => $total,
            'used' => $used,
            'free' => $free,
            'percentage' => round(($used / $total) * 100, 2)
        ];
    }

    private function cleanTempFiles()
    {
        $count = 0;
        $tempPaths = [
            storage_path('app/temp'),
            storage_path('framework/sessions'),
            storage_path('framework/views')
        ];
        
        foreach ($tempPaths as $path) {
            if (is_dir($path)) {
                $files = File::allFiles($path);
                foreach ($files as $file) {
                    if ($file->getMTime() < strtotime('-1 day')) {
                        File::delete($file->getPathname());
                        $count++;
                    }
                }
            }
        }
        
        return $count;
    }

    private function cleanOldLogs()
    {
        $count = 0;
        $logsPath = storage_path('logs');
        
        if (is_dir($logsPath)) {
            $files = File::files($logsPath);
            foreach ($files as $file) {
                if ($file->getMTime() < strtotime('-30 days')) {
                    File::delete($file->getPathname());
                    $count++;
                }
            }
        }
        
        return $count;
    }

    private function cleanUnusedMedia()
    {
        // This would require checking database references
        // For now, return 0 as it needs more complex logic
        return 0;
    }

    private function clearCache()
    {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('route:clear');
        \Artisan::call('view:clear');
    }
}