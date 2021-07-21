<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Billing;

use App\Enums\Models\Billing\BalanceFrequency;
use App\Enums\Models\Billing\Service;
use App\Models\Billing\Balance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class BalanceTest.
 */
class BalanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The service attribute of an balance shall be cast to a Service enum instance.
     *
     * @return void
     */
    public function testCastsServiceToEnum()
    {
        $balance = Balance::factory()->createOne();

        $service = $balance->service;

        static::assertInstanceOf(Service::class, $service);
    }

    /**
     * The frequency attribute of an balance shall be cast to a Frequency enum instance.
     *
     * @return void
     */
    public function testCastsFrequencyToEnum()
    {
        $balance = Balance::factory()->createOne();

        $frequency = $balance->frequency;

        static::assertInstanceOf(BalanceFrequency::class, $frequency);
    }

    /**
     * Balance shall be auditable.
     *
     * @return void
     */
    public function testAuditable()
    {
        Config::set('audit.console', true);

        $balance = Balance::factory()->createOne();

        static::assertEquals(1, $balance->audits()->count());
    }

    /**
     * Balances shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $balance = Balance::factory()->createOne();

        static::assertIsString($balance->getName());
    }
}
