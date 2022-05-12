<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Synonym;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SynonymStoreTest.
 */
class SynonymStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Synonym Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->makeOne();

        $response = $this->post(route('api.animesynonym.store', $synonym->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Synonym Store Endpoint shall require the text field.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->createOne();

        $user->givePermissionTo('create anime synonym');

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animesynonym.store'));

        $response->assertJsonValidationErrors([
            AnimeSynonym::ATTRIBUTE_TEXT,
        ]);
    }

    /**
     * The Synonym Store Endpoint shall create a synonym.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $anime = Anime::factory()->createOne();

        $parameters = array_merge(
            AnimeSynonym::factory()->raw(),
            [AnimeSynonym::ATTRIBUTE_ANIME => $anime->getKey()],
        );

        $user = User::factory()->createOne();

        $user->givePermissionTo('create anime synonym');

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animesynonym.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(AnimeSynonym::TABLE, 1);
    }
}
