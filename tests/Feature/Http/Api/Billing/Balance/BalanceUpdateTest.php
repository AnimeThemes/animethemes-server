<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Balance;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Billing\BalanceFrequency;
use App\Enums\Models\Billing\Service;
use App\Models\Auth\User;
use App\Models\Billing\Balance;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class BalanceUpdateTest.
 */
class BalanceUpdateTest extends TestCase
{
    /**
     * The Balance Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $balance = Balance::factory()->createOne();

        $frequency = Arr::random(BalanceFrequency::cases());
        $service = Arr::random(Service::cases());

        $parameters = array_merge(
            Balance::factory()->raw(),
            [
                Balance::ATTRIBUTE_FREQUENCY => $frequency->localize(),
                Balance::ATTRIBUTE_SERVICE => $service->localize(),
            ]
        );

        $response = $this->put(route('api.balance.update', ['balance' => $balance] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Balance Update Endpoint shall forbid users without the update balance permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $balance = Balance::factory()->createOne();

        $frequency = Arr::random(BalanceFrequency::cases());
        $service = Arr::random(Service::cases());

        $parameters = array_merge(
            Balance::factory()->raw(),
            [
                Balance::ATTRIBUTE_FREQUENCY => $frequency->localize(),
                Balance::ATTRIBUTE_SERVICE => $service->localize(),
            ]
        );

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.balance.update', ['balance' => $balance] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Balance Update Endpoint shall forbid users from updating a balance that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $balance = Balance::factory()->trashed()->createOne();

        $frequency = Arr::random(BalanceFrequency::cases());
        $service = Arr::random(Service::cases());

        $parameters = array_merge(
            Balance::factory()->raw(),
            [
                Balance::ATTRIBUTE_FREQUENCY => $frequency->localize(),
                Balance::ATTRIBUTE_SERVICE => $service->localize(),
            ]
        );

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Balance::class))->createOne();

        Sanctum::actingAs($user);

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

        $frequency = Arr::random(BalanceFrequency::cases());
        $service = Arr::random(Service::cases());

        $parameters = array_merge(
            Balance::factory()->raw(),
            [
                Balance::ATTRIBUTE_FREQUENCY => $frequency->localize(),
                Balance::ATTRIBUTE_SERVICE => $service->localize(),
            ]
        );

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Balance::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.balance.update', ['balance' => $balance] + $parameters));

        $response->assertOk();
    }
}
