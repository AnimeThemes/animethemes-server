<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VideoSeeder extends Seeder
{

    public function run() {
        // Remove any existing rows in Videos table
        // We want this table to match storage, avoiding the need for reconciliation
        DB::table('video')->delete();

        // Get metadata for all objects in storage
        $fs = Storage::disk('spaces');
        $files = $fs->listContents('', true);

        // Create videos from WebM metadata
        // Bulk insertion violates default packet size constraints
        foreach ($files as $file) {
            if ($file['type'] === 'file' && $file['extension'] === 'webm') {
                Video::create(array(
                    'basename' => $file['basename'],
                    'filename' => $file['filename'],
                    'path' => $file['path']
                ));
            }
        }
    }
}
