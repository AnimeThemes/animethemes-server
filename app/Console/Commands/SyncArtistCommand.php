<?php

namespace App\Console\Commands;

use App\Models\Artist;
use DB;
use App\DataManager\RedditParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncArtistCommand extends Command
{
    /**
     * The name and signature of the console command.
     * 
     * @var string
     */
    protected $signature = 'sync-artist';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Artists database table from Artist in /r/AnimeThemes Reddit wiki';

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
        RedditParser::RegisterArtists();

        // Cleanup
        $allArtists = Artist::withCount('themes')->get();
        foreach ($allArtists as $artist) {
            if ($artist->themes_count === 0) {
                Log::info('delete-artist', $artist->toArray());
                $artist->delete();
            }
        }
    }
}
