<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use Illuminate\Console\Command;

class AnnouncementListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'list:announcement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List Welcome Screen Announcements';

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
        // Display Announcements as table
        $headers = ['Alias', 'Content'];

        $announcements = Announcement::all(['alias', 'content'])->toArray();

        $this->table($headers, $announcements);
    }
}
