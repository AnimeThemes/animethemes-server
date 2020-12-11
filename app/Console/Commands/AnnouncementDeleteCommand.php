<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AnnouncementDeleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:announcement
                            {--alias= : An internal identifier to which an announcement can be referred}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Announcement from Welcome Screen';

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
        // Alias is required
        $alias = $this->option('alias');
        if (empty($alias)) {
            LOG::error('alias is required');
            $this->error('alias is required');

            return;
        }

        // Announcement must exist to be deleted
        $announcement = Announcement::where('alias', $alias)->first();
        if (! $announcement) {
            LOG::error("Announcement '{$alias}' does not exist");
            $this->error("Announcement '{$alias}' does not exist");

            return;
        }

        // Delete the Announcement
        $result = $announcement->delete();

        // Confirm deletion of announcement
        if ($result) {
            LOG::info("Announcement '{$alias}' deleted");
            $this->info("Announcement '{$alias}' deleted");
        } else {
            LOG::error("Announcement '{$alias}' was not deleted");
            $this->error("Announcement '{$alias}' was not deleted");
        }
    }
}
