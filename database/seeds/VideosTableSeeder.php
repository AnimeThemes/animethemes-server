<?php

use App\Models\Video;
use Illuminate\Database\Seeder;

class VideosTableSeeder extends Seeder 
{

    public function run() {
        DB::table('videos')->delete();
        $json = File::get('database/data/videos.json');
        $data = json_decode($json);
        foreach ($data as $obj) {
            Video::create(array(
                'alias' => $obj->alias,
                'path' => $obj->path
            ));
        }
    }
}
