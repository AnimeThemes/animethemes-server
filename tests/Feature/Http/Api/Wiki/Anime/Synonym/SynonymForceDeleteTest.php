<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Synonym;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SynonymForceDeleteTest.
 */
class SynonymForceDeleteTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Synonym Force Delete Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $response = $this->delete(route('api.animesynonym.forceDelete', ['animesynonym' => $synonym]));

        $response->assertUnauthorized();
    }

    /**
     * The Synonym Force Delete Endpoint shall forbid users without the force delete anime synonym permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animesynonym.forceDelete', ['animesynonym' => $synonym]));

        $response->assertForbidden();
    }

    /**
     * The Synonym Force Delete Endpoint shall force delete the synonym.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE()->format(AnimeSynonym::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animesynonym.forceDelete', ['animesynonym' => $synonym]));

        $response->assertOk();
        static::assertModelMissing($synonym);
    }
}
