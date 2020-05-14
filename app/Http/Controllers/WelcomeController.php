<?php

namespace App\Http\Controllers;

use App\Grills\GrillFactory;
use App\Models\Announcement;
use App\Models\Video;

class WelcomeController extends Controller
{
    public function do() {
        // View Data
        $grill = GrillFactory::getGrill();
        $videoCount = Video::count();
        $announcements = Announcement::all();

        return view('welcome', [
            'announcements' => $announcements,
            'grill' => $grill->getPath(),
            'videoCount' => $videoCount
        ]);
    }
}
