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
     * Client with forwarded ip shall be rate limited.
     *
     * @return void
     */
    public function testForwardedIpRateLimited(): void
    {
        $response = $this->withHeader('x-forwarded-ip', fake()->ipv4())->get(route('api.anime.index'));

        $response->assertHeader('X-RateLimit-Limit');
        $response->assertHeader('X-RateLimit-Remaining');
    }

    /**
     * Client with no forwarded ip shall not be rate limited.
     *
     * @return void
     */
    public function testClientNoForwardedIpNotRateLimited(): void
    {
        $response = $this->get(route('api.anime.index'));

        $response->assertHeaderMissing('X-RateLimit-Limit');
        $response->assertHeaderMissing('X-RateLimit-Remaining');
    }

    /**
     * Users with the 'bypass api rate limiter' permission  shall not be rate limited.
     *
     * @return void
     */
    public function testUserNotRateLimited(): void
    {
        $user = User::factory()->withPermissions(SpecialPermission::BYPASS_API_RATE_LIMITER->value)->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('api.anime.index'));

        $response->assertHeaderMissing('X-RateLimit-Limit');
        $response->assertHeaderMissing('X-RateLimit-Remaining');
    }
}
