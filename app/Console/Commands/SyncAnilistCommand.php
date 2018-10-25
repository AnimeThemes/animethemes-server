<?php

namespace App\Console\Commands;

use App\DataManager\Mappings\AnilistMapper;
use Illuminate\Console\Command;

class SyncAnilistCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-anilist';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync anilist ids to anime database table';

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
        AnilistMapper::FillDatabase();
    }
}
