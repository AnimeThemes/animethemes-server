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

class EntryDestroyTest extends TestCase
{
    /**
     * The Entry Destroy Endpoint shall be protected by sanctum.
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
     * The Entry Destroy Endpoint shall forbid users without the delete anime theme entry permission.
     */
    public function testForbidden(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animethemeentry.destroy', ['animethemeentry' => $entry]));

        $response->assertForbidden();
    }

    /**
     * The Entry Destroy Endpoint shall forbid users from updating an anime theme entry that is trashed.
     */
    public function testTrashed(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->trashed()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(AnimeThemeEntry::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animethemeentry.destroy', ['animethemeentry' => $entry]));

        $response->assertNotFound();
    }

    /**
     * The Entry Destroy Endpoint shall delete the entry.
     */
    public function testDeleted(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(AnimeThemeEntry::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animethemeentry.destroy', ['animethemeentry' => $entry]));

        $response->assertOk();
        static::assertSoftDeleted($entry);
    }
}
