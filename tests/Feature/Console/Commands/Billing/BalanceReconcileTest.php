<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Billing;

use App\Console\Commands\Billing\BalanceReconcileCommand;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Enums\Models\Billing\Service;
use App\Models\Billing\Balance;
use App\Repositories\Service\DigitalOcean\Billing\DigitalOceanBalanceRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Collection;
use Mockery\MockInterface;
use RuntimeException;
use Tests\TestCase;

/**
 * Class BalanceReconcileTest.
 */
class BalanceReconcileTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

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
     *
     * @psalm-suppress UndefinedMagicMethod
     */
    public function testNoResults()
    {
        $this->mock(DigitalOceanBalanceRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('all')->once()->andReturn(Collection::make());
        });

        $this->artisan(BalanceReconcileCommand::class, ['service' => Service::DIGITALOCEAN()->key])->expectsOutput('No Balances created or deleted or updated');
    }

    /**
     * If balances are created, the Reconcile Balance Command shall output '{Created Count} Balances created, 0 Balances deleted, 0 Balances updated'.
     *
     * @return void
     *
     * @psalm-suppress UndefinedMagicMethod
     */
    public function testCreated()
    {
        $createdBalanceCount = $this->faker->randomDigitNotNull();

        $balances = Balance::factory()
            ->count($createdBalanceCount)
            ->make([
                'date' => Carbon::now()->format(AllowedDateFormat::YMD),
                'service' => Service::DIGITALOCEAN,
            ]);

        $this->mock(DigitalOceanBalanceRepository::class, function (MockInterface $mock) use ($balances) {
            $mock->shouldReceive('all')->once()->andReturn($balances);
        });

        $this->artisan(BalanceReconcileCommand::class, ['service' => Service::DIGITALOCEAN()->key])->expectsOutput("{$createdBalanceCount} Balances created, 0 Balances deleted, 0 Balances updated");
    }

    /**
     * If balances are deleted, the Reconcile Balance Command shall output '0 Balances created, {Deleted Count} Balances deleted, 0 Balances updated'.
     *
     * @return void
     *
     * @psalm-suppress UndefinedMagicMethod
     */
    public function testDeleted()
    {
        $deletedBalanceCount = $this->faker->randomDigitNotNull();

        Balance::factory()
            ->count($deletedBalanceCount)
            ->create([
                'date' => Carbon::now()->format(AllowedDateFormat::YMD),
                'service' => Service::DIGITALOCEAN,
            ]);

        $this->mock(DigitalOceanBalanceRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('all')->once()->andReturn(Collection::make());
        });

        $this->artisan(BalanceReconcileCommand::class, ['service' => Service::DIGITALOCEAN()->key])->expectsOutput("0 Balances created, {$deletedBalanceCount} Balances deleted, 0 Balances updated");
    }

    /**
     * If balances are updated, the Reconcile Balance Command shall output '0 Balances created, 0 Balances deleted, {Updated Count} Balances updated'.
     *
     * @return void
     *
     * @psalm-suppress UndefinedMagicMethod
     */
    public function testUpdated()
    {
        Balance::factory()
            ->create([
                'date' => Carbon::now()->format(AllowedDateFormat::YMD),
                'service' => Service::DIGITALOCEAN,
            ]);

        $sourceBalances = Balance::factory()
            ->make([
                'date' => Carbon::now()->format(AllowedDateFormat::YMD),
                'service' => Service::DIGITALOCEAN,
            ]);

        $this->mock(DigitalOceanBalanceRepository::class, function (MockInterface $mock) use ($sourceBalances) {
            $mock->shouldReceive('all')->once()->andReturn(collect([$sourceBalances]));
        });

        $this->artisan(BalanceReconcileCommand::class, ['service' => Service::DIGITALOCEAN()->key])->expectsOutput('0 Balances created, 0 Balances deleted, 1 Balances updated');
    }
}
