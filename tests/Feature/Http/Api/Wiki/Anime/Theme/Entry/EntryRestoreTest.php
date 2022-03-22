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
 * Class EntryRestoreTest.
 */
class EntryRestoreTest extends TestCase
{
    /**
     * The Entry Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Event::fakeExcept(ThemeCreating::class);

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $entry->delete();

        $response = $this->patch(route('api.animethemeentry.restore', ['animethemeentry' => $entry]));

        $response->assertForbidden();
    }

    /**
     * The Entry Restore Endpoint shall restore the entry.
     *
     * @return void
     */
    public function testRestored(): void
    {
        Event::fakeExcept(ThemeCreating::class);

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $entry->delete();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['entry:restore']
        );

        $response = $this->patch(route('api.animethemeentry.restore', ['animethemeentry' => $entry]));

        $response->assertOk();
        static::assertNotSoftDeleted($entry);
    }
}
