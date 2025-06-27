<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Synonym;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SynonymUpdateTest.
 */
class SynonymUpdateTest extends TestCase
{
    /**
     * The Synonym Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $parameters = AnimeSynonym::factory()->raw();

        $response = $this->put(route('api.animesynonym.update', ['animesynonym' => $synonym] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Synonym Update Endpoint shall forbid users without the update anime synonym permission.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $parameters = AnimeSynonym::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.animesynonym.update', ['animesynonym' => $synonym] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Synonym Update Endpoint shall forbid users from updating an anime synonym that is trashed.
     *
     * @return void
     */
    public function test_trashed(): void
    {
        $synonym = AnimeSynonym::factory()
            ->trashed()
            ->for(Anime::factory())
            ->createOne();

        $parameters = AnimeSynonym::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::VIEW->format(AnimeSynonym::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.animesynonym.update', ['animesynonym' => $synonym] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Synonym Update Endpoint shall update a synonym.
     *
     * @return void
     */
    public function test_update(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $parameters = AnimeSynonym::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(AnimeSynonym::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.animesynonym.update', ['animesynonym' => $synonym] + $parameters));

        $response->assertOk();
    }
}
