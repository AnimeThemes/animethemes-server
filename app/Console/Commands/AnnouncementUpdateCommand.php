<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AnnouncementUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:announcement
                            {--alias= : An internal identifier to which an announcement can be referred}
                            {--content= : The Announcement Text}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Announcement for Welcome Screen';

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

        // Content is not required
        $content = $this->option('content');

        // Announcement must exist to be updated
        $announcement = Announcement::where('alias', $alias)->first();
        if (! $announcement) {
            LOG::error("Announcement '{$alias}' does not exist");
            $this->error("Announcement '{$alias}' does not exist");
            return;
        }

        // Update Content if set
        if (! empty($content)) {
            $announcement->content = $content;
        }

        // Save changes if needed
        $result = false;
        if ($announcement->isDirty()) {
            $result = $announcement->save();
        }

        // Confirm if changes were made
        if ($result) {
            LOG::info("Announcement '{$alias}' updated");
            $this->info("Announcement '{$alias}' updated");
        } else {
            LOG::error("Announcement '{$alias}' was not updated");
            $this->error("Announcement '{$alias}' was not updated");
        }
    }
}
