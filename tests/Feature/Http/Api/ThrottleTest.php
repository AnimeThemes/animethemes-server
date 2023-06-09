<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api;

use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ThrottleTest.
 */
class ThrottleTest extends TestCase
{
    /**
     * By default, the user shall be rate limited when using the API.
     *
     * @return void
     */
    public function testRateLimited(): void
    {
        $response = $this->get(route('api.anime.index'));

        $response->assertHeader('X-RateLimit-Limit');
        $response->assertHeader('X-RateLimit-Remaining');
    }

    /**
     * Users with the 'bypass api rate limiter' permission  shall not be rate limited.
     *
     * @return void
     */
    public function testClientNotRateLimited(): void
    {
        $user = User::factory()->withPermissions(SpecialPermission::BYPASS_API_RATE_LIMITER->value)->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('api.anime.index'));

        $response->assertHeaderMissing('X-RateLimit-Limit');
        $response->assertHeaderMissing('X-RateLimit-Remaining');
    }
}
