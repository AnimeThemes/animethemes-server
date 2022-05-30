<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Billing;

use App\Console\Commands\Billing\Balance\BalanceReconcileCommand;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Enums\Models\Billing\Service;
use App\Models\Billing\Balance;
use App\Repositories\Service\DigitalOcean\Billing\DigitalOceanBalanceRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Mockery\MockInterface;
use RuntimeException;
use Tests\TestCase;

/**
 * Class BalanceReconcileTest.
 */
class BalanceReconcileTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Reconcile Balance Command shall require a 'service' argument.
     *
     * @return void
     */
    public function testServiceArgumentRequired(): void
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
    public function testOther(): void
    {
        $other = Service::OTHER()->key;

        $this->artisan(BalanceReconcileCommand::class, ['service' => $other])->expectsOutput("No source repository implemented for Service '$other'");
    }

    /**
     * If no changes are needed, the Reconcile Balance Command shall output 'No Balances created or deleted or updated'.
     *
     * @return void
     */
    public function testNoResults(): void
    {
        $this->mock(DigitalOceanBalanceRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $this->artisan(BalanceReconcileCommand::class, ['service' => Service::DIGITALOCEAN()->key])->expectsOutput('No Balances created or deleted or updated');
    }

    /**
     * If balances are created, the Reconcile Balance Command shall output '{Created Count} Balances created, 0 Balances deleted, 0 Balances updated'.
     *
     * @return void
     */
    public function testCreated(): void
    {
        $this->baseRefreshDatabase(); // Cannot lazily refresh database within pending command

        $createdBalanceCount = $this->faker->randomDigitNotNull();

        $balances = Balance::factory()
            ->count($createdBalanceCount)
            ->make([
                Balance::ATTRIBUTE_DATE => Date::now()->format(AllowedDateFormat::YMD),
                Balance::ATTRIBUTE_SERVICE => Service::DIGITALOCEAN,
            ]);

        $this->mock(DigitalOceanBalanceRepository::class, function (MockInterface $mock) use ($balances) {
            $mock->shouldReceive('get')->once()->andReturn($balances);
        });

        $this->artisan(BalanceReconcileCommand::class, ['service' => Service::DIGITALOCEAN()->key])->expectsOutput("$createdBalanceCount Balances created, 0 Balances deleted, 0 Balances updated");
    }

    /**
     * If balances are deleted, the Reconcile Balance Command shall output '0 Balances created, {Deleted Count} Balances deleted, 0 Balances updated'.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $deletedBalanceCount = $this->faker->randomDigitNotNull();

        Balance::factory()
            ->count($deletedBalanceCount)
            ->create([
                Balance::ATTRIBUTE_DATE => Date::now()->format(AllowedDateFormat::YMD),
                Balance::ATTRIBUTE_SERVICE => Service::DIGITALOCEAN,
            ]);

        $this->mock(DigitalOceanBalanceRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $this->artisan(BalanceReconcileCommand::class, ['service' => Service::DIGITALOCEAN()->key])->expectsOutput("0 Balances created, $deletedBalanceCount Balances deleted, 0 Balances updated");
    }

    /**
     * If balances are updated, the Reconcile Balance Command shall output '0 Balances created, 0 Balances deleted, {Updated Count} Balances updated'.
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

        $this->mock(DigitalOceanBalanceRepository::class, function (MockInterface $mock) use ($sourceBalances) {
            $mock->shouldReceive('get')->once()->andReturn(collect([$sourceBalances]));
        });

        $this->artisan(BalanceReconcileCommand::class, ['service' => Service::DIGITALOCEAN()->key])->expectsOutput('0 Balances created, 0 Balances deleted, 1 Balances updated');
    }
}
