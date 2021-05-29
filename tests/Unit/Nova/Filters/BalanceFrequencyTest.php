<?php

namespace Tests\Unit\Nova\Filters;

use App\Enums\Billing\Frequency;
use App\Models\Billing\Balance;
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
     * The Balance Frequency Filter shall have an option for each Frequency instance.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(BalanceFrequencyFilter::class);

        foreach (Frequency::getInstances() as $frequency) {
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
        $frequency = Frequency::getRandomInstance();

        Balance::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = $this->novaFilter(BalanceFrequencyFilter::class);

        $response = $filter->apply(Balance::class, $frequency->value);

        $filteredBalances = Balance::where('frequency', $frequency->value)->get();
        foreach ($filteredBalances as $filteredBalance) {
            $response->assertContains($filteredBalance);
        }
        $response->assertCount($filteredBalances->count());
    }
}
