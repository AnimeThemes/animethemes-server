<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\AnimeThemeEntryVideo;

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
 * Class AnimeThemeEntryVideoStoreTest.
 */
class AnimeThemeEntryVideoStoreTest extends TestCase
{
    /**
     * The Anime Theme Entry Video Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->create();

        $video = Video::factory()->createOne();

        $response = $this->post(route('api.animethemeentryvideo.store', ['animethemeentry' => $entry, 'video' => $video]));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Theme Entry Video Store Endpoint shall forbid users without the create anime theme entry & create video permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->create();

        $video = Video::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animethemeentryvideo.store', ['animethemeentry' => $entry, 'video' => $video]));

        $response->assertForbidden();
    }

    /**
     * The Anime Theme Entry Video Store Endpoint shall create an Anime Theme Entry Video.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->create();

        $video = Video::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE()->format(AnimeThemeEntry::class),
                CrudPermission::CREATE()->format(Video::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animethemeentryvideo.store', ['animethemeentry' => $entry, 'video' => $video]));

        $response->assertCreated();
        static::assertDatabaseCount(AnimeThemeEntryVideo::TABLE, 1);
    }
}
