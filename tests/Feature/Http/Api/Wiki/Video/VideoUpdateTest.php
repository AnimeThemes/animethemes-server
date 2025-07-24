<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\Auth\User;
use App\Models\Wiki\Video;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VideoUpdateTest extends TestCase
{
    /**
     * The Video Update Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $video = Video::factory()->createOne();

        $overlap = Arr::random(VideoOverlap::cases());
        $source = Arr::random(VideoSource::cases());

        $parameters = array_merge(
            Video::factory()->raw(),
            [
                Video::ATTRIBUTE_OVERLAP => $overlap->localize(),
                Video::ATTRIBUTE_SOURCE => $source->localize(),
            ]
        );

        $response = $this->put(route('api.video.update', ['video' => $video] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Video Update Endpoint shall forbid users without the update video permission.
     */
    public function testForbidden(): void
    {
        $video = Video::factory()->createOne();

        $overlap = Arr::random(VideoOverlap::cases());
        $source = Arr::random(VideoSource::cases());

        $parameters = array_merge(
            Video::factory()->raw(),
            [
                Video::ATTRIBUTE_OVERLAP => $overlap->localize(),
                Video::ATTRIBUTE_SOURCE => $source->localize(),
            ]
        );

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.video.update', ['video' => $video] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Video Update Endpoint shall forbid users from updating a video that is trashed.
     */
    public function testTrashed(): void
    {
        $video = Video::factory()->trashed()->createOne();

        $overlap = Arr::random(VideoOverlap::cases());
        $source = Arr::random(VideoSource::cases());

        $parameters = array_merge(
            Video::factory()->raw(),
            [
                Video::ATTRIBUTE_OVERLAP => $overlap->localize(),
                Video::ATTRIBUTE_SOURCE => $source->localize(),
            ]
        );

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Video::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.video.update', ['video' => $video] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Video Update Endpoint shall update a video.
     */
    public function testUpdate(): void
    {
        $video = Video::factory()->createOne();

        $overlap = Arr::random(VideoOverlap::cases());
        $source = Arr::random(VideoSource::cases());

        $parameters = array_merge(
            Video::factory()->raw(),
            [
                Video::ATTRIBUTE_OVERLAP => $overlap->localize(),
                Video::ATTRIBUTE_SOURCE => $source->localize(),
            ]
        );

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Video::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.video.update', ['video' => $video] + $parameters));

        $response->assertOk();
    }
}
