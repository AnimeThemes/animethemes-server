<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Auth\User\Me\List\ExternalProfile;

use App\Enums\Auth\CrudPermission;
use App\Http\Api\Query\Query;
use App\Http\Resources\List\Collection\ExternalProfileCollection;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MyExternalProfileIndexTest extends TestCase
{
    use WithFaker;

    /**
     * The My External Profile Index Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $response = $this->get(route('api.me.externalprofile.index'));

        $response->assertUnauthorized();
    }

    /**
     * The My External Profile Index Endpoint shall forbid users without the view external profile permission.
     */
    public function testForbiddenIfMissingPermission(): void
    {
        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('api.me.externalprofile.index'));

        $response->assertForbidden();
    }

    /**
     * The My External Profile Index Endpoint shall return profiles owned by the user.
     */
    public function testOnlySeesOwnedProfiles(): void
    {
        ExternalProfile::factory()
            ->for(User::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        ExternalProfile::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $user = User::factory()->withPermissions(CrudPermission::VIEW->format(ExternalProfile::class))->createOne();

        $profileCount = $this->faker->randomDigitNotNull();

        $profiles = ExternalProfile::factory()
            ->for($user)
            ->count($profileCount)
            ->create();

        Sanctum::actingAs($user);

        $response = $this->get(route('api.me.externalprofile.index'));

        $response->assertJsonCount($profileCount, ExternalProfileCollection::$wrap);

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalProfileCollection($profiles, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
