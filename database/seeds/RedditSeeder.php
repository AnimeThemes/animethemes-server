<?php

use App\Models\Anime;
use App\Models\Theme;
use App\Models\Video;
use App\DataManager\RedditParser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class RedditSeeder extends Seeder 
{
    public function run() {
        RedditParser::RegisterCollections();
    }
}
