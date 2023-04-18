<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\Auth\User;
use App\Models\Wiki\Video;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class VideoUpdateTest.
 */
class VideoUpdateTest extends TestCase
{
    /**
     * The Video Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $video = Video::factory()->createOne();

        $parameters = array_merge(
            Video::factory()->raw(),
            [
                Video::ATTRIBUTE_OVERLAP => VideoOverlap::getRandomInstance()->description,
                Video::ATTRIBUTE_SOURCE => VideoSource::getRandomInstance()->description,
            ]
        );

        $response = $this->put(route('api.video.update', ['video' => $video] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Video Update Endpoint shall forbid users without the update video permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $video = Video::factory()->createOne();

        $parameters = array_merge(
            Video::factory()->raw(),
            [
                Video::ATTRIBUTE_OVERLAP => VideoOverlap::getRandomInstance()->description,
                Video::ATTRIBUTE_SOURCE => VideoSource::getRandomInstance()->description,
            ]
        );

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.video.update', ['video' => $video] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Video Update Endpoint shall forbid users from updating a video that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $video = Video::factory()->createOne();

        $video->delete();

        $parameters = array_merge(
            Video::factory()->raw(),
            [
                Video::ATTRIBUTE_OVERLAP => VideoOverlap::getRandomInstance()->description,
                Video::ATTRIBUTE_SOURCE => VideoSource::getRandomInstance()->description,
            ]
        );

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(Video::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.video.update', ['video' => $video] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Video Update Endpoint shall update a video.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $video = Video::factory()->createOne();

        $parameters = array_merge(
            Video::factory()->raw(),
            [
                Video::ATTRIBUTE_OVERLAP => VideoOverlap::getRandomInstance()->description,
                Video::ATTRIBUTE_SOURCE => VideoSource::getRandomInstance()->description,
            ]
        );

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(Video::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.video.update', ['video' => $video] + $parameters));

        $response->assertOk();
    }
}
