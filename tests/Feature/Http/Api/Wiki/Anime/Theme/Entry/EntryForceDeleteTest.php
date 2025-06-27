<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme\Entry;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class EntryForceDeleteTest.
 */
class EntryForceDeleteTest extends TestCase
{
    /**
     * The Entry Force Delete Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $response = $this->delete(route('api.animethemeentry.forceDelete', ['animethemeentry' => $entry]));

        $response->assertUnauthorized();
    }

    /**
     * The Entry Force Delete Endpoint shall forbid users without the force delete anime theme entry permission.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animethemeentry.forceDelete', ['animethemeentry' => $entry]));

        $response->assertForbidden();
    }

    /**
     * The Entry Force Delete Endpoint shall force delete the entry.
     *
     * @return void
     */
    public function test_deleted(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(AnimeThemeEntry::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animethemeentry.forceDelete', ['animethemeentry' => $entry]));

        $response->assertOk();
        static::assertModelMissing($entry);
    }
}
