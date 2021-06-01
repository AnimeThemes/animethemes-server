<?php

declare(strict_types=1);

namespace Console\Billing;

use App\Console\Commands\Billing\TransactionReconcileCommand;
use App\Enums\Billing\Service;
use App\Models\Billing\Transaction;
use App\Repositories\Service\Billing\DigitalOceanTransactionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Exception\RuntimeException;
use Tests\TestCase;

/**
 * Class TransactionReconcileTest
 * @package Console\Billing
 */
class TransactionReconcileTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Reconcile Transaction Command shall require a 'service' argument.
     *
     * @return void
     */
    public function testServiceArgumentRequired()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "service").');

        $this->artisan(TransactionReconcileCommand::class)->run();
    }

    /**
     * When service is Other, the Reconcile Transaction Command shall output "No source repository implemented for Service 'other'".
     *
     * @return void
     */
    public function testOther()
    {
        $other = Service::OTHER()->key;

        $this->artisan(TransactionReconcileCommand::class, ['service' => $other])->expectsOutput("No source repository implemented for Service '{$other}'");
    }

    /**
     * If no changes are needed, the Reconcile Transaction Command shall output 'No Transactions created or deleted or updated'.
     *
     * @return void
     */
    public function testNoResults()
    {
        $mock = $this->mock(DigitalOceanTransactionRepository::class);

        $mock->shouldReceive('all')
            ->once()
            ->andReturn(Collection::make());

        $this->app->instance(DigitalOceanTransactionRepository::class, $mock);

        $this->artisan(TransactionReconcileCommand::class, ['service' => Service::DIGITALOCEAN()->key])->expectsOutput('No Transactions created or deleted or updated');
    }

    /**
     * If transactions are created, the Reconcile Transaction Command shall output '{Created Count} Transactions created, 0 Transactions deleted, 0 Transactions updated'.
     *
     * @return void
     */
    public function testCreated()
    {
        $createdTransactionCount = $this->faker->randomDigitNotNull;

        $transactions = Transaction::factory()
            ->count($createdTransactionCount)
            ->make([
                'service' => Service::DIGITALOCEAN,
            ]);

        $mock = $this->mock(DigitalOceanTransactionRepository::class);

        $mock->shouldReceive('all')
            ->once()
            ->andReturn($transactions);

        $this->app->instance(DigitalOceanTransactionRepository::class, $mock);

        $this->artisan(TransactionReconcileCommand::class, ['service' => Service::DIGITALOCEAN()->key])->expectsOutput("{$createdTransactionCount} Transactions created, 0 Transactions deleted, 0 Transactions updated");
    }

    /**
     * If transactions are deleted, the Reconcile Transaction Command shall output '0 Transactions created, {Deleted Count} Transactions deleted, 0 Transactions updated'.
     *
     * @return void
     */
    public function testDeleted()
    {
        $deletedTransactionCount = $this->faker->randomDigitNotNull;

        Transaction::factory()
            ->count($deletedTransactionCount)
            ->create([
                'service' => Service::DIGITALOCEAN,
            ]);

        $mock = $this->mock(DigitalOceanTransactionRepository::class);

        $mock->shouldReceive('all')
            ->once()
            ->andReturn(Collection::make());

        $this->app->instance(DigitalOceanTransactionRepository::class, $mock);

        $this->artisan(TransactionReconcileCommand::class, ['service' => Service::DIGITALOCEAN()->key])->expectsOutput("0 Transactions created, {$deletedTransactionCount} Transactions deleted, 0 Transactions updated");
    }
}
