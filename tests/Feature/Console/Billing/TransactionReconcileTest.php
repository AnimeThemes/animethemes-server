<?php

namespace Tests\Feature\Console\Billing;

use App\Console\Commands\Billing\TransactionReconcileCommand;
use App\Enums\Billing\Service;
use App\Models\Billing\Transaction;
use App\Repositories\Service\Billing\DigitalOceanTransactionRepository;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Exception\RuntimeException;
use Tests\TestCase;

class TransactionReconcileTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutEvents;

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
        $created_transaction_count = $this->faker->randomDigitNotNull;

        $transactions = Transaction::factory()
            ->count($created_transaction_count)
            ->make([
                'service' => Service::DIGITALOCEAN,
            ]);

        $mock = $this->mock(DigitalOceanTransactionRepository::class);

        $mock->shouldReceive('all')
            ->once()
            ->andReturn($transactions);

        $this->app->instance(DigitalOceanTransactionRepository::class, $mock);

        $this->artisan(TransactionReconcileCommand::class, ['service' => Service::DIGITALOCEAN()->key])->expectsOutput("{$created_transaction_count} Transactions created, 0 Transactions deleted, 0 Transactions updated");
    }

    /**
     * If transactions are deleted, the Reconcile Transaction Command shall output '0 Transactions created, {Deleted Count} Transactions deleted, 0 Transactions updated'.
     *
     * @return void
     */
    public function testDeleted()
    {
        $deleted_transaction_count = $this->faker->randomDigitNotNull;

        Transaction::factory()
            ->count($deleted_transaction_count)
            ->create([
                'service' => Service::DIGITALOCEAN,
            ]);

        $mock = $this->mock(DigitalOceanTransactionRepository::class);

        $mock->shouldReceive('all')
            ->once()
            ->andReturn(Collection::make());

        $this->app->instance(DigitalOceanTransactionRepository::class, $mock);

        $this->artisan(TransactionReconcileCommand::class, ['service' => Service::DIGITALOCEAN()->key])->expectsOutput("0 Transactions created, {$deleted_transaction_count} Transactions deleted, 0 Transactions updated");
    }

    /**
     * If transactions are updated, the Reconcile Transaction Command shall output '0 Transactions created, 0 Transactions deleted, {Updated Count} Transactions updated'.
     *
     * @return void
     */
    public function testUpdated()
    {
        $updated_transaction_count = $this->faker->randomDigitNotNull;

        Transaction::factory()
            ->count($updated_transaction_count)
            ->state(new Sequence(
                ['external_id' => 1],
                ['external_id' => 2],
                ['external_id' => 3],
                ['external_id' => 4],
                ['external_id' => 5],
                ['external_id' => 6],
                ['external_id' => 7],
                ['external_id' => 8],
                ['external_id' => 9],
            ))
            ->create([
                'service' => Service::DIGITALOCEAN,
            ]);

        $source_transactions = Transaction::factory()
            ->count($updated_transaction_count)
            ->state(new Sequence(
                ['external_id' => 1],
                ['external_id' => 2],
                ['external_id' => 3],
                ['external_id' => 4],
                ['external_id' => 5],
                ['external_id' => 6],
                ['external_id' => 7],
                ['external_id' => 8],
                ['external_id' => 9],
            ))
            ->make([
                'service' => Service::DIGITALOCEAN,
            ]);

        $mock = $this->mock(DigitalOceanTransactionRepository::class);

        $mock->shouldReceive('all')
            ->once()
            ->andReturn($source_transactions);

        $this->app->instance(DigitalOceanTransactionRepository::class, $mock);

        $this->artisan(TransactionReconcileCommand::class, ['service' => Service::DIGITALOCEAN()->key])->expectsOutput("0 Transactions created, 0 Transactions deleted, {$updated_transaction_count} Transactions updated");
    }
}
