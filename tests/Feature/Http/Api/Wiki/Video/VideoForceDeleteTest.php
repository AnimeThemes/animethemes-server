<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video;

use App\Models\Auth\User;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class VideoForceDeleteTest.
 */
class VideoForceDeleteTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Video Force Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $video = Video::factory()->createOne();

        $response = $this->delete(route('api.video.forceDelete', ['video' => $video]));

        $response->assertUnauthorized();
    }

    /**
     * The Video Force Destroy Endpoint shall force delete the video.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $video = Video::factory()->createOne();

        $user = User::factory()->withPermission('force delete video')->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.video.forceDelete', ['video' => $video]));

        $response->assertOk();
        static::assertModelMissing($video);
    }
}
