<?php

namespace App\Console\Commands;

use App\Models\Anime;
use App\Models\Theme;
use DB;
use App\DataManager\RedditParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncRedditCommand extends Command
{
    /**
     * The name and signature of the console command.
     * 
     * Clean up tables
     * Get Upstream data = array()
     * Check Anime match
     * If yes, check themes and videos
     *
     * @var string
     */
    protected $signature = 'sync-reddit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Anime and Theme database table from Year in /r/AnimeThemes Reddit wiki';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {   
        // Stage 1: Sync Data
        RedditParser::RegisterCollections();

        // Stage 2: Clear empty Themes
        foreach(Theme::withCount('videos')->get() as $theme) {
            if ($theme->videos_count === 0) {
                Log::info("theme-delete", $theme->toArray());
                $theme->delete();
            }
        }

        // Stage 3: Clear Empty Animes Generated
        foreach (Anime::withCount('themes')->get() as $anime) {
            if ($anime->themes_count === 0) {
                Log::info("anime-delete", $anime->toArray());
                $anime->names()->delete();
                $anime->delete();
            }
        }
    }
}
