<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Filters\Billing;

use App\Enums\Models\Billing\Service;
use App\Models\Billing\Balance;
use App\Nova\Filters\Billing\ServiceFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class BillingServiceTest.
 */
class BillingServiceTest extends TestCase
{
    use NovaFilterTest;
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Balance Service Filter shall be a select filter.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter()
    {
        static::novaFilter(ServiceFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Balance Service Filter shall have an option for each Service instance.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testOptions()
    {
        $filter = static::novaFilter(ServiceFilter::class);

        foreach (Service::getInstances() as $service) {
            $filter->assertHasOption($service->description);
        }
    }

    /**
     * The Balance Service Filter shall filter Balances By Service.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter()
    {
        $service = Service::getRandomInstance();

        Balance::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = static::novaFilter(ServiceFilter::class);

        $response = $filter->apply(Balance::class, $service->value);

        $filteredBalances = Balance::where('service', $service->value)->get();
        foreach ($filteredBalances as $filteredBalance) {
            $response->assertContains($filteredBalance);
        }
        $response->assertCount($filteredBalances->count());
    }
}
