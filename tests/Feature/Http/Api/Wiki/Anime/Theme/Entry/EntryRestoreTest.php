<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme\Entry;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class EntryRestoreTest.
 */
class EntryRestoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Entry Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $entry->delete();

        $response = $this->patch(route('api.animethemeentry.restore', ['animethemeentry' => $entry]));

        $response->assertUnauthorized();
    }

    /**
     * The Entry Restore Endpoint shall forbid users without the restore anime theme entry permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $entry->delete();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.animethemeentry.restore', ['animethemeentry' => $entry]));

        $response->assertForbidden();
    }

    /**
     * The Entry Restore Endpoint shall forbid users from restoring an anime theme entry that isn't trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE()->format(AnimeThemeEntry::class))->createOne();

        Sanctum::actingAs($user);

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
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $entry->delete();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE()->format(AnimeThemeEntry::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.animethemeentry.restore', ['animethemeentry' => $entry]));

        $response->assertOk();
        static::assertNotSoftDeleted($entry);
    }
}
