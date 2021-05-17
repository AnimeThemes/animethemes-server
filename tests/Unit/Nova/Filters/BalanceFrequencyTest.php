<?php

namespace Tests\Unit\Nova\Filters;

use App\Enums\BillingFrequency;
use App\Models\Balance;
use App\Nova\Filters\BalanceFrequencyFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class BalanceFrequencyTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Balance Frequency Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(BalanceFrequencyFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Balance Frequency Filter shall have an option for each BillingFrequency instance.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(BalanceFrequencyFilter::class);

        foreach (BillingFrequency::getInstances() as $frequency) {
            $filter->assertHasOption($frequency->description);
        }
    }

    /**
     * The Balance Frequency Filter shall filter Balances By Frequency.
     *
     * @return void
     */
    public function testFilter()
    {
        $frequency = BillingFrequency::getRandomInstance();

        Balance::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = $this->novaFilter(BalanceFrequencyFilter::class);

        $response = $filter->apply(Balance::class, $frequency->value);

        $filtered_balances = Balance::where('frequency', $frequency->value)->get();
        foreach ($filtered_balances as $filtered_balance) {
            $response->assertContains($filtered_balance);
        }
        $response->assertCount($filtered_balances->count());
    }
}
