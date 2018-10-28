<?php

namespace App\Console\Commands;

use App\Models\Serie;
use DB;
use App\DataManager\RedditParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncSeriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-series';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Series table from Series in /r/AnimeThemes Reddit wiki';

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

        // Cleanup
        $allSeries = Serie::withCount('animes')->get();
        foreach ($allSeries as $serie) {
            if ($serie->animes_count === 0) {
                Log::info('delete-artist', $artist->toArray());
                $serie->delete();
            }
        }
    }
}
