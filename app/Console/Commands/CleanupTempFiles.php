<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:temp-files {--hours=24 : Hours after which temp files should be deleted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up temporary files older than specified hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');
        $cutoffTime = Carbon::now()->subHours($hours);
        
        $this->info("🧹 Cleaning up temporary files older than {$hours} hours...");
        
        // Create temp directory if it doesn't exist
        if (!Storage::disk('local')->exists('temp')) {
            Storage::disk('local')->makeDirectory('temp');
            $this->info('📁 Created temp directory.');
        }
        
        $tempFiles = Storage::disk('local')->files('temp');
        $deletedCount = 0;
        $totalSize = 0;
        
        if (empty($tempFiles)) {
            $this->info('✨ No temporary files found.');
            return Command::SUCCESS;
        }
        
        $this->info("📋 Found " . count($tempFiles) . " temporary files. Checking expiry...");
        
        foreach ($tempFiles as $file) {
            $filePath = storage_path('app/' . $file);
            
            if (file_exists($filePath)) {
                $fileTime = Carbon::createFromTimestamp(filemtime($filePath));
                $fileName = basename($file);
                
                if ($fileTime->lt($cutoffTime)) {
                    $fileSize = filesize($filePath);
                    $totalSize += $fileSize;
                    
                    Storage::disk('local')->delete($file);
                    $deletedCount++;
                    
                    $this->line("🗑️  Deleted: {$fileName} (" . $this->formatBytes($fileSize) . ") - Created: " . $fileTime->diffForHumans());
                } else {
                    $this->line("⏰ Keeping: {$fileName} - Created: " . $fileTime->diffForHumans());
                }
            }
        }
        
        if ($deletedCount > 0) {
            $this->newLine();
            $this->info("✅ Successfully deleted {$deletedCount} temporary files");
            $this->info("💾 Freed up " . $this->formatBytes($totalSize) . " of disk space");
        } else {
            $this->info("✨ No temporary files found that are older than {$hours} hours");
        }
        
        // Clean up expired sessions
        $this->cleanupExpiredSessions();
        
        return Command::SUCCESS;
    }
    
    /**
     * Clean up expired download sessions
     */
    private function cleanupExpiredSessions()
    {
        $this->info("🔍 Checking for expired download sessions...");
        
        // This would require accessing session storage
        // For now, just show info
        $this->line("ℹ️  Note: Download sessions are automatically cleaned up when accessed.");
    }
    
    /**
     * Format bytes into human readable format
     */
    private function formatBytes($size, $precision = 2)
    {
        if ($size === 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}