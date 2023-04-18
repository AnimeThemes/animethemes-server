<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme\Entry;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class EntryStoreTest.
 */
class EntryStoreTest extends TestCase
{
    /**
     * The Entry Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->makeOne();

        $response = $this->post(route('api.animethemeentry.store', $entry->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Entry Store Endpoint shall forbid users without the create anime theme entry permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animethemeentry.store', $entry->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Entry Store Endpoint shall require the theme_id field.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(AnimeThemeEntry::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animethemeentry.store'));

        $response->assertJsonValidationErrors([
            AnimeThemeEntry::ATTRIBUTE_THEME,
        ]);
    }

    /**
     * The Entry Store Endpoint shall create an entry.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

        $parameters = array_merge(
            AnimeThemeEntry::factory()->raw(),
            [AnimeThemeEntry::ATTRIBUTE_THEME => $theme->getKey()],
        );

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(AnimeThemeEntry::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animethemeentry.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(AnimeThemeEntry::TABLE, 1);
    }
}
