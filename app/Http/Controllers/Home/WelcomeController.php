<?php

declare(strict_types=1);

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Admin\Announcement;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Spatie\RouteDiscovery\Attributes\Route;

/**
 * Class WelcomeController.
 */
class WelcomeController extends Controller
{
    /**
     * Displays home page.
     *
     * @return View|Factory
     */
    #[Route(fullUri: '/', name: 'welcome')]
    public function show(): View|Factory
    {
        return view('welcome', [
            'announcements' => Announcement::all([Announcement::ATTRIBUTE_CONTENT]),
        ]);
    }
}
