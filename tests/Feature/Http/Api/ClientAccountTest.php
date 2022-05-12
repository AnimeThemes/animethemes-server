<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api;

use App\Constants\Config\WikiConstants;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ClientAccountTest.
 */
class ClientAccountTest extends TestCase
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
     * The configured wiki client account shall not be rate limited.
     *
     * @return void
     */
    public function testClientNotRateLimited(): void
    {
        $client = User::factory()->createOne();

        Config::set(WikiConstants::CLIENT_ACCOUNT_SETTING_QUALIFIED, $client->getKey());

        Sanctum::actingAs($client, ['read anime']);

        $response = $this->get(route('api.anime.index'));

        $response->assertHeaderMissing('X-RateLimit-Limit');
        $response->assertHeaderMissing('X-RateLimit-Remaining');
    }
}
