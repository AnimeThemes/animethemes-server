<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AnnouncementCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:announcement
                            {--alias= : An internal identifier with which an announcement can be referred}
                            {--content= : The Announcement Text}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Announcement for Welcome Screen';

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

        // Content is required
        $content = $this->option('content');
        if (empty($content)) {
            LOG::error('content is required');
            $this->error('content is required');

            return;
        }

        // Alias should be unique
        $announcement = Announcement::where('alias', $alias)->first();
        if ($announcement) {
            LOG::error("Announcement '{$alias}' already exists");
            $this->error("Announcement '{$alias}' already exists");

            return;
        }

        // Create the Announcement
        $result = Announcement::create([
            'alias' => $alias,
            'content' => $content,
        ]);

        // Confirm if Announcement was created
        if ($result->exists()) {
            LOG::info("Announcement '{$alias}' created");
            $this->info("Announcement '{$alias}' created");
        } else {
            LOG::error("Announcement '{$alias}' was not created");
            $this->error("Announcement '{$alias}' was not created");
        }
    }
}
