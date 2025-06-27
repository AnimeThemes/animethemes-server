<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Video;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class VideoForceDeleteTest.
 */
class VideoForceDeleteTest extends TestCase
{
    /**
     * The Video Force Delete Endpoint shall be protected by sanctum.
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
     * The Video Force Delete Endpoint shall forbid users without the force delete video permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $video = Video::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.video.forceDelete', ['video' => $video]));

        $response->assertForbidden();
    }

    /**
     * The Video Force Delete Endpoint shall force delete the video.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $video = Video::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Video::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.video.forceDelete', ['video' => $video]));

        $response->assertOk();
        static::assertModelMissing($video);
    }
}
