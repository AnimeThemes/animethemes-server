<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme\Entry;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class EntryStoreTest.
 */
class EntryStoreTest extends TestCase
{
    use WithoutEvents;

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
     * The Entry Store Endpoint shall require the theme_id field.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermission('create anime theme entry')->createOne();

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

        $user = User::factory()->withPermission('create anime theme entry')->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animethemeentry.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(AnimeThemeEntry::TABLE, 1);
    }
}
