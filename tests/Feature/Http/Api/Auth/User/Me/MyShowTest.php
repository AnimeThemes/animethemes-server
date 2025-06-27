<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Auth\User\Me;

use App\Http\Api\Query\Query;
use App\Http\Resources\Auth\Resource\UserResource;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class MyShowTest.
 */
class MyShowTest extends TestCase
{
    use WithFaker;

    /**
     * The My Show Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $response = $this->get(route('api.me.show'));

        $response->assertUnauthorized();
    }

    /**
     * The My Show Endpoint shall return the resource of the current user.
     *
     * @return void
     */
    public function test_default(): void
    {
        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('api.me.show'));

        $response->assertJson(
            json_decode(
                json_encode(
                    new UserResource($user, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
