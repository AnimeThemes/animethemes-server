<?php

namespace Tests\Unit\Nova\Filters;

use App\Enums\BillingService;
use App\Models\Balance;
use App\Nova\Filters\BillingServiceFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class BillingServiceTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Balance Service Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(BillingServiceFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Balance Service Filter shall have an option for each BillingService instance.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(BillingServiceFilter::class);

        foreach (BillingService::getInstances() as $service) {
            $filter->assertHasOption($service->description);
        }
    }

    /**
     * The Balance Service Filter shall filter Balances By Service.
     *
     * @return void
     */
    public function testFilter()
    {
        $service = BillingService::getRandomInstance();

        Balance::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = $this->novaFilter(BillingServiceFilter::class);

        $response = $filter->apply(Balance::class, $service->value);

        $filtered_balances = Balance::where('service', $service->value)->get();
        foreach ($filtered_balances as $filtered_balance) {
            $response->assertContains($filtered_balance);
        }
        $response->assertCount($filtered_balances->count());
    }
}
