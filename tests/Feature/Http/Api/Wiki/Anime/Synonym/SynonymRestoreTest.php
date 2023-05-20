<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Synonym;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SynonymRestoreTest.
 */
class SynonymRestoreTest extends TestCase
{
    /**
     * The Synonym Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $synonym = AnimeSynonym::factory()
            ->trashed()
            ->for(Anime::factory())
            ->createOne();

        $response = $this->patch(route('api.animesynonym.restore', ['animesynonym' => $synonym]));

        $response->assertUnauthorized();
    }

    /**
     * The Synonym Restore Endpoint shall forbid users without the restore anime synonym permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $synonym = AnimeSynonym::factory()
            ->trashed()
            ->for(Anime::factory())
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.animesynonym.restore', ['animesynonym' => $synonym]));

        $response->assertForbidden();
    }

    /**
     * The Synonym Restore Endpoint shall forbid users from restoring an anime synonym that isn't trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE()->format(AnimeSynonym::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.animesynonym.restore', ['animesynonym' => $synonym]));

        $response->assertForbidden();
    }

    /**
     * The Synonym Restore Endpoint shall restore the synonym.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $synonym = AnimeSynonym::factory()
            ->trashed()
            ->for(Anime::factory())
            ->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE()->format(AnimeSynonym::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.animesynonym.restore', ['animesynonym' => $synonym]));

        $response->assertOk();
        static::assertNotSoftDeleted($synonym);
    }
}
