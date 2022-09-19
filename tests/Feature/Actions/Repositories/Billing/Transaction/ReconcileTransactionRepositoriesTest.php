<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Repositories\Billing\Transaction;

use App\Actions\Repositories\Billing\Transaction\ReconcileTransactionRepositoriesAction;
use App\Enums\Actions\ActionStatus;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Enums\Models\Billing\Service;
use App\Models\Billing\Transaction;
use App\Repositories\DigitalOcean\Billing\DigitalOceanTransactionRepository as DigitalOceanSourceRepository;
use App\Repositories\Eloquent\Billing\DigitalOceanTransactionRepository as DigitalOceanDestinationRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Class ReconcileTransactionRepositoriesTest.
 */
class ReconcileTransactionRepositoriesTest extends TestCase
{
    use WithFaker;

    /**
     * If no changes are needed, the Reconcile Transaction Repository Action shall indicate no changes were made.
     *
     * @return void
     */
    public function testNoResults(): void
    {
        $this->mock(DigitalOceanSourceRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $action = new ReconcileTransactionRepositoriesAction();

        $source = App::make(DigitalOceanSourceRepository::class);
        $destination = App::make(DigitalOceanDestinationRepository::class);

        $result = $action->reconcileRepositories($source, $destination);

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertFalse($result->hasChanges());
        static::assertDatabaseCount(Transaction::class, 0);
    }

    /**
     * If transactions are created, the Reconcile Transaction Repository Action shall return created transactions.
     *
     * @return void
     */
    public function testCreated(): void
    {
        $createdTransactionCount = $this->faker->numberBetween(2, 9);

        $transactions = Transaction::factory()
            ->count($createdTransactionCount)
            ->make([
                Transaction::ATTRIBUTE_DATE => Date::now()->format(AllowedDateFormat::YMD),
                Transaction::ATTRIBUTE_SERVICE => Service::DIGITALOCEAN,
            ]);

        $this->mock(DigitalOceanSourceRepository::class, function (MockInterface $mock) use ($transactions) {
            $mock->shouldReceive('get')->once()->andReturn($transactions);
        });

        $action = new ReconcileTransactionRepositoriesAction();

        $source = App::make(DigitalOceanSourceRepository::class);
        $destination = App::make(DigitalOceanDestinationRepository::class);

        $result = $action->reconcileRepositories($source, $destination);

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertTrue($result->hasChanges());
        static::assertCount($createdTransactionCount, $result->getCreated());
        static::assertDatabaseCount(Transaction::class, $createdTransactionCount);
    }

    /**
     * If transactions are deleted, the Reconcile Transaction Repository Action shall return deleted transactions.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $deletedTransactionCount = $this->faker->numberBetween(2, 9);

        $transactions = Transaction::factory()
            ->count($deletedTransactionCount)
            ->create([
                Transaction::ATTRIBUTE_DATE => Date::now()->format(AllowedDateFormat::YMD),
                Transaction::ATTRIBUTE_SERVICE => Service::DIGITALOCEAN,
            ]);

        $this->mock(DigitalOceanSourceRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $action = new ReconcileTransactionRepositoriesAction();

        $source = App::make(DigitalOceanSourceRepository::class);
        $destination = App::make(DigitalOceanDestinationRepository::class);

        $result = $action->reconcileRepositories($source, $destination);

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertTrue($result->hasChanges());
        static::assertCount($deletedTransactionCount, $result->getDeleted());

        static::assertDatabaseCount(Transaction::class, $deletedTransactionCount);
        foreach ($transactions as $transaction) {
            static::assertSoftDeleted($transaction);
        }
    }
}
