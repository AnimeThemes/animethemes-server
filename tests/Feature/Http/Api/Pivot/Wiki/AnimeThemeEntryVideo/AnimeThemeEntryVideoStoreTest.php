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
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnimeThemeEntryVideoStoreTest.
 */
class AnimeThemeEntryVideoStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Anime Theme Entry Video Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->makeOne();

        $response = $this->post(route('api.animethemeentryvideo.store', $entryVideo->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Theme Entry Video Store Endpoint shall forbid users without the create anime theme entry & create video permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animethemeentryvideo.store', $entryVideo->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Anime Theme Entry Video Store Endpoint shall require anime theme entry and video fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE()->format(AnimeThemeEntry::class),
                CrudPermission::CREATE()->format(Video::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animethemeentryvideo.store'));

        $response->assertJsonValidationErrors([
            AnimeThemeEntryVideo::ATTRIBUTE_ENTRY,
            AnimeThemeEntryVideo::ATTRIBUTE_VIDEO,
        ]);
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
            ->createOne();

        $parameters = [
            AnimeThemeEntryVideo::ATTRIBUTE_ENTRY => $entry->getKey(),
            AnimeThemeEntryVideo::ATTRIBUTE_VIDEO => Video::factory()->createOne()->getKey(),
        ];

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE()->format(AnimeThemeEntry::class),
                CrudPermission::CREATE()->format(Video::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animethemeentryvideo.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(AnimeThemeEntryVideo::TABLE, 1);
    }
}
