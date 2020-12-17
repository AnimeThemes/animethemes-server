<?php

namespace Tests\Feature\Http\Api;

use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The Announcement Index Endpoint shall display the Announcement attributes.
     *
     * @return void
     */
    public function testAnnouncementIndexAttributes()
    {
        $announcements = Announcement::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.announcement.index'));

        $response->assertJson([
            'announcements' => $announcements->map(function ($announcement) {
                return static::getData($announcement);
            })->toArray(),
        ]);
    }

    /**
     * The Show Announcement Endpoint shall display the Announcement attributes.
     *
     * @return void
     */
    public function testShowAnnouncementAttributes()
    {
        $announcement = Announcement::factory()->create();

        $response = $this->get(route('api.announcement.show', ['announcement' => $announcement]));

        $response->assertJson(static::getData($announcement));
    }

    /**
     * Get attributes for Announcement resource.
     *
     * @param Announcement $announcement
     * @return array
     */
    public static function getData(Announcement $announcement)
    {
        return [
            'id' => $announcement->announcement_id,
            'content' => $announcement->content,
            'created_at' => $announcement->created_at->toJSON(),
            'updated_at' => $announcement->updated_at->toJSON(),
        ];
    }
}
