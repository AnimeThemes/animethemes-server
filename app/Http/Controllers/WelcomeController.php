<?php

namespace App\Http\Controllers;

use App\Grills\GrillFactory;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WelcomeController extends Controller
{
    public function do() {
        LOG::info('Page Visit - Welcome');

        // View Data
        $grill = GrillFactory::getGrill();
        $videoCount = Video::count();

        return view('welcome', [
            'grill' => $grill->getPath(),
            'videoCount' => $videoCount
        ]);
    }
}
