<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Repositories\Billing\Balance;

use App\Actions\Repositories\Billing\Balance\ReconcileBalanceRepositoriesAction;
use App\Enums\Actions\ActionStatus;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Enums\Models\Billing\Service;
use App\Models\Billing\Balance;
use App\Repositories\DigitalOcean\Billing\DigitalOceanBalanceRepository as DigitalOceanSourceRepository;
use App\Repositories\Eloquent\Billing\DigitalOceanBalanceRepository as DigitalOceanDestinationRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Class ReconcileBalanceRepositoriesTest.
 */
class ReconcileBalanceRepositoriesTest extends TestCase
{
    use WithFaker;

    /**
     * If no changes are needed, the Reconcile Balance Repository Action shall indicate no changes were made.
     *
     * @return void
     */
    public function testNoResults(): void
    {
        $this->mock(DigitalOceanSourceRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $action = new ReconcileBalanceRepositoriesAction();

        $source = App::make(DigitalOceanSourceRepository::class);
        $destination = App::make(DigitalOceanDestinationRepository::class);

        $result = $action->reconcileRepositories($source, $destination);

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertFalse($result->hasChanges());
        static::assertDatabaseCount(Balance::class, 0);
    }

    /**
     * If balances are created, the Reconcile Balance Repository Action shall return created balances.
     *
     * @return void
     */
    public function testCreated(): void
    {
        $createdBalanceCount = $this->faker->numberBetween(2, 9);

        $balances = Balance::factory()
            ->count($createdBalanceCount)
            ->make([
                Balance::ATTRIBUTE_DATE => Date::now()->format(AllowedDateFormat::YMD),
                Balance::ATTRIBUTE_SERVICE => Service::DIGITALOCEAN,
            ]);

        $this->mock(DigitalOceanSourceRepository::class, function (MockInterface $mock) use ($balances) {
            $mock->shouldReceive('get')->once()->andReturn($balances);
        });

        $action = new ReconcileBalanceRepositoriesAction();

        $source = App::make(DigitalOceanSourceRepository::class);
        $destination = App::make(DigitalOceanDestinationRepository::class);

        $result = $action->reconcileRepositories($source, $destination);

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertTrue($result->hasChanges());
        static::assertCount($createdBalanceCount, $result->getCreated());
        static::assertDatabaseCount(Balance::class, $createdBalanceCount);
    }

    /**
     * If balances are deleted, the Reconcile Balance Repository Action shall return deleted balances.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $deletedBalanceCount = $this->faker->numberBetween(2, 9);

        $balances = Balance::factory()
            ->count($deletedBalanceCount)
            ->create([
                Balance::ATTRIBUTE_DATE => Date::now()->format(AllowedDateFormat::YMD),
                Balance::ATTRIBUTE_SERVICE => Service::DIGITALOCEAN,
            ]);

        $this->mock(DigitalOceanSourceRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $action = new ReconcileBalanceRepositoriesAction();

        $source = App::make(DigitalOceanSourceRepository::class);
        $destination = App::make(DigitalOceanDestinationRepository::class);

        $result = $action->reconcileRepositories($source, $destination);

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertTrue($result->hasChanges());
        static::assertCount($deletedBalanceCount, $result->getDeleted());

        static::assertDatabaseCount(Balance::class, $deletedBalanceCount);
        foreach ($balances as $balance) {
            static::assertSoftDeleted($balance);
        }
    }

    /**
     * If balances are updated, the Reconcile Balance Repository Action shall return updated balances.
     *
     * @return void
     */
    public function testUpdated(): void
    {
        Balance::factory()
            ->create([
                Balance::ATTRIBUTE_DATE => Date::now()->format(AllowedDateFormat::YMD),
                Balance::ATTRIBUTE_SERVICE => Service::DIGITALOCEAN,
            ]);

        $sourceBalances = Balance::factory()
            ->make([
                Balance::ATTRIBUTE_DATE => Date::now()->format(AllowedDateFormat::YMD),
                Balance::ATTRIBUTE_SERVICE => Service::DIGITALOCEAN,
            ]);

        $this->mock(DigitalOceanSourceRepository::class, function (MockInterface $mock) use ($sourceBalances) {
            $mock->shouldReceive('get')->once()->andReturn(collect([$sourceBalances]));
        });

        $action = new ReconcileBalanceRepositoriesAction();

        $source = App::make(DigitalOceanSourceRepository::class);
        $destination = App::make(DigitalOceanDestinationRepository::class);

        $result = $action->reconcileRepositories($source, $destination);

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertTrue($result->hasChanges());
        static::assertCount(1, $result->getUpdated());
        static::assertDatabaseCount(Balance::class, 1);
    }
}
