<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme\Entry;

use App\Events\Wiki\Anime\Theme\ThemeCreating;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Support\Facades\Event;
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
        Event::fakeExcept(ThemeCreating::class);

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
        $this->withoutEvents();

        $user = User::factory()->createOne();

        $user->givePermissionTo('create anime theme entry');

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
        Event::fakeExcept(ThemeCreating::class);

        $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

        $parameters = array_merge(
            AnimeThemeEntry::factory()->raw(),
            [AnimeThemeEntry::ATTRIBUTE_THEME => $theme->getKey()],
        );

        $user = User::factory()->createOne();

        $user->givePermissionTo('create anime theme entry');

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animethemeentry.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(AnimeThemeEntry::TABLE, 1);
    }
}
