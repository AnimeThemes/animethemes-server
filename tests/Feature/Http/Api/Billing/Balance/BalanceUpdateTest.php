<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Balance;

use App\Enums\Models\Billing\BalanceFrequency;
use App\Enums\Models\Billing\Service;
use App\Models\Auth\User;
use App\Models\Billing\Balance;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class BalanceUpdateTest.
 */
class BalanceUpdateTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Balance Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $balance = Balance::factory()->createOne();

        $parameters = array_merge(
            Balance::factory()->raw(),
            [
                Balance::ATTRIBUTE_FREQUENCY => BalanceFrequency::getRandomInstance()->description,
                Balance::ATTRIBUTE_SERVICE => Service::getRandomInstance()->description,
            ]
        );

        $response = $this->put(route('api.balance.update', ['balance' => $balance] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Balance Update Endpoint shall update a balance.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $balance = Balance::factory()->createOne();

        $parameters = array_merge(
            Balance::factory()->raw(),
            [
                Balance::ATTRIBUTE_FREQUENCY => BalanceFrequency::getRandomInstance()->description,
                Balance::ATTRIBUTE_SERVICE => Service::getRandomInstance()->description,
            ]
        );

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('admin')->createOne(),
            ['balance:update']
        );

        $response = $this->put(route('api.balance.update', ['balance' => $balance] + $parameters));

        $response->assertOk();
    }
}
