<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\DataManager\Mappings\KitsuMapper;

class SyncKitsuCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-kitsu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync kitsu ids to anime database table';

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
        KitsuMapper::FillDatabase();
    }
}
