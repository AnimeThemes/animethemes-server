<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Models\Admin\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class WelcomeTest.
 */
class WelcomeTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The welcome route shall display the home screen.
     *
     * @return void
     */
    public function testWelcome()
    {
        $response = $this->get(route('welcome'));

        $response->assertViewIs('welcome');
    }

    /**
     * The welcome route shall display the content of all announcements.
     *
     * @return void
     */
    public function testWelcomeAnnouncements()
    {
        $announcements = collect(Announcement::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create());

        $response = $this->get(route('welcome'));

        foreach ($announcements as $announcement) {
            $response->assertSee($announcement->content);
        }
    }
}
