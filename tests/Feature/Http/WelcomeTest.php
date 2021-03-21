<?php

namespace Tests\Feature\Http;

use App\Models\Announcement;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class WelcomeTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The welcome route shall display the home screen.
     *
     * @return void
     */
    public function testWelcome()
    {
        $response = $this->get(route('welcome.index'));

        $response->assertViewIs('welcome');
    }

    /**
     * The welcome route shall display the content of all announcements.
     *
     * @return void
     */
    public function testWelcomeAnnouncements()
    {
        $announcements = Announcement::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('welcome.index'));

        foreach ($announcements as $announcement) {
            $response->assertSee($announcement->content);
        }
    }
}
