<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Video;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VideoRestoreTest extends TestCase
{
    /**
     * The Video Restore Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $video = Video::factory()->trashed()->createOne();

        $response = $this->patch(route('api.video.restore', ['video' => $video]));

        $response->assertUnauthorized();
    }

    /**
     * The Video Restore Endpoint shall forbid users without the restore video permission.
     */
    public function testForbidden(): void
    {
        $video = Video::factory()->trashed()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.video.restore', ['video' => $video]));

        $response->assertForbidden();
    }

    /**
     * The Video Restore Endpoint shall forbid users from restoring a video that isn't trashed.
     */
    public function testTrashed(): void
    {
        $video = Video::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Video::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.video.restore', ['video' => $video]));

        $response->assertForbidden();
    }

    /**
     * The Video Restore Endpoint shall restore the video.
     */
    public function testRestored(): void
    {
        $video = Video::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Video::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.video.restore', ['video' => $video]));

        $response->assertOk();
        static::assertNotSoftDeleted($video);
    }
}
