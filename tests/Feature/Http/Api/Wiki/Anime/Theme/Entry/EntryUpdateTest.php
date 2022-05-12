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
 * Class EntryUpdateTest.
 */
class EntryUpdateTest extends TestCase
{
    /**
     * The Entry Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Event::fakeExcept(ThemeCreating::class);

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $parameters = AnimeThemeEntry::factory()->raw();

        $response = $this->put(route('api.animethemeentry.update', ['animethemeentry' => $entry] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Entry Update Endpoint shall update an entry.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        Event::fakeExcept(ThemeCreating::class);

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $parameters = AnimeThemeEntry::factory()->raw();

        $user = User::factory()->createOne();

        $user->givePermissionTo('update anime theme entry');

        Sanctum::actingAs($user);

        $response = $this->put(route('api.animethemeentry.update', ['animethemeentry' => $entry] + $parameters));

        $response->assertOk();
    }
}
