<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video;

use App\Models\Auth\User;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class VideoRestoreTest.
 */
class VideoRestoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Video Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $video = Video::factory()->createOne();

        $video->delete();

        $response = $this->patch(route('api.video.restore', ['video' => $video]));

        $response->assertUnauthorized();
    }

    /**
     * The Video Restore Endpoint shall restore the video.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $video = Video::factory()->createOne();

        $video->delete();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('admin')->createOne(),
            ['video:restore']
        );

        $response = $this->patch(route('api.video.restore', ['video' => $video]));

        $response->assertOk();
        static::assertNotSoftDeleted($video);
    }
}
