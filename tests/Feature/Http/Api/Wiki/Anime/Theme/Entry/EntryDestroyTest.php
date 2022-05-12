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
 * Class EntryDestroyTest.
 */
class EntryDestroyTest extends TestCase
{
    /**
     * The Entry Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Event::fakeExcept(ThemeCreating::class);

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $response = $this->delete(route('api.animethemeentry.destroy', ['animethemeentry' => $entry]));

        $response->assertUnauthorized();
    }

    /**
     * The Entry Destroy Endpoint shall delete the entry.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        Event::fakeExcept(ThemeCreating::class);

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $user = User::factory()->createOne();

        $user->givePermissionTo('delete anime theme entry');

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animethemeentry.destroy', ['animethemeentry' => $entry]));

        $response->assertOk();
        static::assertSoftDeleted($entry);
    }
}
