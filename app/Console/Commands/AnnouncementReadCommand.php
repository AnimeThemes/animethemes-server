<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AnnouncementReadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'read:announcement
                            {--alias= : An internal identifier to which an announcement can be referred}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read Welcome Screen Announcement by Alias';

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

        // Announcement must exist to be read
        $announcement = Announcement::where('alias', $alias)->first();
        if (! $announcement) {
            LOG::error("Announcement '{$alias}' does not exist");
            $this->error("Announcement '{$alias}' does not exist");

            return;
        }

        // Display Announcement as table
        $headers = ['Alias', 'Content'];

        $data = [
            [
                'alias' => $announcement->alias,
                'content' => $announcement->content,
            ],
        ];

        $this->table($headers, $data);
    }
}
