<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VideoSeeder extends Seeder
{
    public function run()
    {
        // Get metadata for all objects in storage
        $fs = Storage::disk('spaces');
        $files = $fs->listContents('', true);

        // Create videos from WebM metadata
        // Bulk insertion violates default packet size constraints
        foreach ($files as $file) {
            if ($file['type'] === 'file' && $file['extension'] === 'webm') {
                $video = Video::where('basename', $file['basename'])->first();
                if ($video === null) {
                    $basename = $file['basename'];
                    Log::info("Creating video '{$basename}'");
                    Video::create([
                        'basename' => $file['basename'],
                        'filename' => $file['filename'],
                        'path' => $file['path'],
                        'size' => $file['size'],
                    ]);
                }
            }
        }
    }
}
