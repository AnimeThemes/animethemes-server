<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Filters\Billing\Balance;

use App\Enums\Models\Billing\BalanceFrequency;
use App\Models\Billing\Balance;
use App\Nova\Filters\Billing\Balance\BalanceFrequencyFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class BalanceFrequencyTest.
 */
class BalanceFrequencyTest extends TestCase
{
    use NovaFilterTest;
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Balance Frequency Filter shall be a select filter.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter()
    {
        static::novaFilter(BalanceFrequencyFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Balance Frequency Filter shall have an option for each Frequency instance.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testOptions()
    {
        $filter = static::novaFilter(BalanceFrequencyFilter::class);

        foreach (BalanceFrequency::getInstances() as $frequency) {
            $filter->assertHasOption($frequency->description);
        }
    }

    /**
     * The Balance Frequency Filter shall filter Balances By Frequency.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter()
    {
        $frequency = BalanceFrequency::getRandomInstance();

        Balance::factory()->count($this->faker->randomDigitNotNull())->create();

        $filter = static::novaFilter(BalanceFrequencyFilter::class);

        $response = $filter->apply(Balance::class, $frequency->value);

        $filteredBalances = Balance::query()->where('frequency', $frequency->value)->get();
        foreach ($filteredBalances as $filteredBalance) {
            $response->assertContains($filteredBalance);
        }
        $response->assertCount($filteredBalances->count());
    }
}
