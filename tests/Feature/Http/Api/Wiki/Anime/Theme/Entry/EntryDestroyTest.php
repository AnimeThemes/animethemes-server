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
 * Class EntryDestroyTest.
 */
class EntryDestroyTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Entry Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
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
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $user = User::factory()->withPermission('delete anime theme entry')->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animethemeentry.destroy', ['animethemeentry' => $entry]));

        $response->assertOk();
        static::assertSoftDeleted($entry);
    }
}
