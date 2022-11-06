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
 * Class EntryUpdateTest.
 */
class EntryUpdateTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Entry Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $parameters = AnimeThemeEntry::factory()->raw();

        $response = $this->put(route('api.animethemeentry.update', ['animethemeentry' => $entry] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Entry Update Endpoint shall forbid users without the update anime theme entry permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $parameters = AnimeThemeEntry::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.animethemeentry.update', ['animethemeentry' => $entry] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Entry Update Endpoint shall forbid users from updating an anime theme entry that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $entry->delete();

        $parameters = AnimeThemeEntry::factory()->raw();

        $user = User::factory()->withPermission('update anime theme entry')->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.animethemeentry.update', ['animethemeentry' => $entry] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Entry Update Endpoint shall update an entry.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $parameters = AnimeThemeEntry::factory()->raw();

        $user = User::factory()->withPermission('update anime theme entry')->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.animethemeentry.update', ['animethemeentry' => $entry] + $parameters));

        $response->assertOk();
    }
}
