<?php

namespace App\Http\Controllers;

use App\Grills\Grill;
use App\Models\Announcement;
use App\Models\Video;

class WelcomeController extends Controller
{
    /**
     * Displays home page.
     *
     * @return  \Illuminate\View\View
     */
    public function do()
    {
        // View Data
        $grill = Grill::random();
        $videoCount = Video::count();
        $announcements = Announcement::all();

        return view('welcome', [
            'announcements' => $announcements,
            'grill' => $grill,
            'videoCount' => $videoCount,
        ]);
    }
}
