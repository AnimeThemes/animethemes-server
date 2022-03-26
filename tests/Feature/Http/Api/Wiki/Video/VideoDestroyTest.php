<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video;

use App\Models\Auth\User;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class VideoDestroyTest.
 */
class VideoDestroyTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Video Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $video = Video::factory()->createOne();

        $response = $this->delete(route('api.video.destroy', ['video' => $video]));

        $response->assertForbidden();
    }

    /**
     * The Video Destroy Endpoint shall delete the video.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $video = Video::factory()->createOne();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('admin')->createOne(),
            ['video:delete']
        );

        $response = $this->delete(route('api.video.destroy', ['video' => $video]));

        $response->assertOk();
        static::assertSoftDeleted($video);
    }
}
