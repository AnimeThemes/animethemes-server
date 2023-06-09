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
 * Class BalanceStoreTest.
 */
class BalanceStoreTest extends TestCase
{
    /**
     * The Balance Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $balance = Balance::factory()->makeOne();

        $response = $this->post(route('api.balance.store', $balance->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Balance Store Endpoint shall forbid users without the create balance permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $balance = Balance::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.balance.store', $balance->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Balance Store Endpoint shall require date, service, frequency, usage & balance fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Balance::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.balance.store'));

        $response->assertJsonValidationErrors([
            Balance::ATTRIBUTE_DATE,
            Balance::ATTRIBUTE_SERVICE,
            Balance::ATTRIBUTE_FREQUENCY,
            Balance::ATTRIBUTE_USAGE,
            Balance::ATTRIBUTE_BALANCE,
        ]);
    }

    /**
     * The Balance Store Endpoint shall create a balance.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $frequency = Arr::random(BalanceFrequency::cases());
        $service = Arr::random(Service::cases());

        $parameters = array_merge(
            Balance::factory()->raw(),
            [
                Balance::ATTRIBUTE_FREQUENCY => $frequency->localize(),
                Balance::ATTRIBUTE_SERVICE => $service->localize(),
            ]
        );

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Balance::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.balance.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Balance::class, 1);
    }
}
