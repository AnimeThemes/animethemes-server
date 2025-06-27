<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\AnimeThemeEntryVideo;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnimeThemeEntryVideoDestroyTest.
 */
class AnimeThemeEntryVideoDestroyTest extends TestCase
{
    /**
     * The Anime Theme Entry Video Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $response = $this->delete(route('api.animethemeentryvideo.destroy', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video]));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Theme Entry Video Destroy Endpoint shall forbid users without the delete anime theme entry & delete video permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animethemeentryvideo.destroy', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video]));

        $response->assertForbidden();
    }

    /**
     * The Anime Theme Entry Video Destroy Endpoint shall return an error if the anime theme entry video does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->create();

        $video = Video::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(AnimeThemeEntry::class),
                CrudPermission::DELETE->format(Video::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animethemeentryvideo.destroy', ['animethemeentry' => $entry, 'video' => $video]));

        $response->assertNotFound();
    }

    /**
     * The Anime Theme Entry Video Destroy Endpoint shall delete the anime theme entry video.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(AnimeThemeEntry::class),
                CrudPermission::DELETE->format(Video::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animethemeentryvideo.destroy', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video]));

        $response->assertOk();
        static::assertModelMissing($entryVideo);
    }
}
