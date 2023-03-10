<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video;

use App\Enums\Auth\CrudPermission;
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

        $response->assertUnauthorized();
    }

    /**
     * The Video Destroy Endpoint shall forbid users without the delete video permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $video = Video::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.video.destroy', ['video' => $video]));

        $response->assertForbidden();
    }

    /**
     * The Video Destroy Endpoint shall forbid users from updating a video that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $video = Video::factory()->createOne();

        $video->delete();

        $user = User::factory()->withPermission(CrudPermission::DELETE()->format(Video::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.video.destroy', ['video' => $video]));

        $response->assertNotFound();
    }

    /**
     * The Video Destroy Endpoint shall delete the video.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $video = Video::factory()->createOne();

        $user = User::factory()->withPermission(CrudPermission::DELETE()->format(Video::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.video.destroy', ['video' => $video]));

        $response->assertOk();
        static::assertSoftDeleted($video);
    }
}
