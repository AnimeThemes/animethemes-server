<?php

use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class VideosTableSeeder extends Seeder 
{

    public function run() {
        DB::table('videos')->delete();

        $fs = Storage::disk('spaces');
        $files = $fs->listContents('', true);

        foreach ($files as $file) {
            $isFile = $file['type'] == 'file';
            if ($isFile) {
                Video::create(array(
                    'basename' => $file['basename'],
                    'filename' => $file['filename'],
                    'path' => $file['path']
                ));
            }
        }
    }
}
