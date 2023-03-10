<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Synonym;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SynonymDestroyTest.
 */
class SynonymDestroyTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Synonym Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $response = $this->delete(route('api.animesynonym.destroy', ['animesynonym' => $synonym]));

        $response->assertUnauthorized();
    }

    /**
     * The Synonym Destroy Endpoint shall forbid users without the delete anime synonym permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animesynonym.destroy', ['animesynonym' => $synonym]));

        $response->assertForbidden();
    }

    /**
     * The Synonym Destroy Endpoint shall forbid users from updating an anime synonym that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $synonym->delete();

        $user = User::factory()->withPermission(CrudPermission::DELETE()->format(AnimeSynonym::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animesynonym.destroy', ['animesynonym' => $synonym]));

        $response->assertNotFound();
    }

    /**
     * The Synonym Destroy Endpoint shall delete the synonym.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $user = User::factory()->withPermission(CrudPermission::DELETE()->format(AnimeSynonym::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animesynonym.destroy', ['animesynonym' => $synonym]));

        $response->assertOk();
        static::assertSoftDeleted($synonym);
    }
}
