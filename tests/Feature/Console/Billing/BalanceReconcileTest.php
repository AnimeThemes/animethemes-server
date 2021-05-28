<?php

namespace Tests\Feature\Console\Billing;

use App\Console\Commands\Billing\BalanceReconcileCommand;
use App\Enums\Billing\Service;
use App\Enums\Filter\AllowedDateFormat;
use App\Models\Billing\Balance;
use App\Repositories\Service\Billing\DigitalOceanBalanceRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Collection;
use RuntimeException;
use Tests\TestCase;

class BalanceReconcileTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Reconcile Balance Command shall require a 'service' argument.
     *
     * @return void
     */
    public function testServiceArgumentRequired()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "service").');

        $this->artisan(BalanceReconcileCommand::class)->run();
    }

    /**
     * When service is Other, the Reconcile Balance Command shall output "No source repository implemented for Service 'other'".
     *
     * @return void
     */
    public function testOther()
    {
        $other = Service::OTHER()->key;

        $this->artisan(BalanceReconcileCommand::class, ['service' => $other])->expectsOutput("No source repository implemented for Service '{$other}'");
    }

    /**
     * If no changes are needed, the Reconcile Balance Command shall output 'No Balances created or deleted or updated'.
     *
     * @return void
     */
    public function testNoResults()
    {
        $mock = $this->mock(DigitalOceanBalanceRepository::class);

        $mock->shouldReceive('all')
            ->once()
            ->andReturn(Collection::make());

        $this->app->instance(DigitalOceanBalanceRepository::class, $mock);

        $this->artisan(BalanceReconcileCommand::class, ['service' => Service::DIGITALOCEAN()->key])->expectsOutput('No Balances created or deleted or updated');
    }

    /**
     * If balances are created, the Reconcile Balance Command shall output '{Created Count} Balances created, 0 Balances deleted, 0 Balances updated'.
     *
     * @return void
     */
    public function testCreated()
    {
        $created_balance_count = $this->faker->randomDigitNotNull;

        $balances = Balance::factory()
            ->count($created_balance_count)
            ->make([
                'date' => Carbon::now()->format(AllowedDateFormat::WITH_DAY),
                'service' => Service::DIGITALOCEAN,
            ]);

        $mock = $this->mock(DigitalOceanBalanceRepository::class);

        $mock->shouldReceive('all')
            ->once()
            ->andReturn($balances);

        $this->app->instance(DigitalOceanBalanceRepository::class, $mock);

        $this->artisan(BalanceReconcileCommand::class, ['service' => Service::DIGITALOCEAN()->key])->expectsOutput("{$created_balance_count} Balances created, 0 Balances deleted, 0 Balances updated");
    }

    /**
     * If balances are deleted, the Reconcile Balance Command shall output '0 Balances created, {Deleted Count} Balances deleted, 0 Balances updated'.
     *
     * @return void
     */
    public function testDeleted()
    {
        $deleted_balance_count = $this->faker->randomDigitNotNull;

        Balance::factory()
            ->count($deleted_balance_count)
            ->create([
                'date' => Carbon::now()->format(AllowedDateFormat::WITH_DAY),
                'service' => Service::DIGITALOCEAN,
            ]);

        $mock = $this->mock(DigitalOceanBalanceRepository::class);

        $mock->shouldReceive('all')
            ->once()
            ->andReturn(Collection::make());

        $this->app->instance(DigitalOceanBalanceRepository::class, $mock);

        $this->artisan(BalanceReconcileCommand::class, ['service' => Service::DIGITALOCEAN()->key])->expectsOutput("0 Balances created, {$deleted_balance_count} Balances deleted, 0 Balances updated");
    }

    /**
     * If balances are updated, the Reconcile Balance Command shall output '0 Balances created, 0 Balances deleted, {Updated Count} Balances updated'.
     *
     * @return void
     */
    public function testUpdated()
    {
        $updated_balance_count = $this->faker->randomDigitNotNull;;

        Balance::factory()
            ->create([
                'date' => Carbon::now()->format(AllowedDateFormat::WITH_DAY),
                'service' => Service::DIGITALOCEAN,
            ]);

        $source_balances = Balance::factory()
            ->make([
                'date' => Carbon::now()->format(AllowedDateFormat::WITH_DAY),
                'service' => Service::DIGITALOCEAN,
            ]);

        $mock = $this->mock(DigitalOceanBalanceRepository::class);

        $mock->shouldReceive('all')
            ->once()
            ->andReturn(collect([$source_balances]));

        $this->app->instance(DigitalOceanBalanceRepository::class, $mock);

        $this->artisan(BalanceReconcileCommand::class, ['service' => Service::DIGITALOCEAN()->key])->expectsOutput("0 Balances created, 0 Balances deleted, {$updated_balance_count} Balances updated");
    }
}
