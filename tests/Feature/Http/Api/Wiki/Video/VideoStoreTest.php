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

class VideoStoreTest extends TestCase
{
    /**
     * The Video Store Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $video = Video::factory()->makeOne();

        $response = $this->post(route('api.video.store', $video->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Video Store Endpoint shall forbid users without the create video permission.
     */
    public function testForbidden(): void
    {
        $video = Video::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.video.store', $video->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Video Store Endpoint shall require basename, filename, mimetype, path & size fields.
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Video::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.video.store'));

        $response->assertJsonValidationErrors([
            Video::ATTRIBUTE_BASENAME,
            Video::ATTRIBUTE_FILENAME,
            Video::ATTRIBUTE_MIMETYPE,
            Video::ATTRIBUTE_PATH,
            Video::ATTRIBUTE_SIZE,
        ]);
    }

    /**
     * The Video Store Endpoint shall create a video.
     */
    public function testCreate(): void
    {
        $overlap = Arr::random(VideoOverlap::cases());
        $source = Arr::random(VideoSource::cases());

        $parameters = array_merge(
            Video::factory()->raw(),
            [
                Video::ATTRIBUTE_OVERLAP => $overlap->localize(),
                Video::ATTRIBUTE_SOURCE => $source->localize(),
            ]
        );

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Video::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.video.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Video::class, 1);
    }
}
