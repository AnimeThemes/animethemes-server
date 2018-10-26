<?php

namespace App\Console\Commands;

use App\Models\Video;
use App\Models\Theme;
use App\Models\Anime;
use DB;
use App\DataManager\RedditParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncSeriesCommand extends Command
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
    protected $signature = 'sync-series';

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
        RedditParser::RegisterSeries();
    }
}
