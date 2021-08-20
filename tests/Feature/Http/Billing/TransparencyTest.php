<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Billing;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Models\Billing\Balance;
use App\Models\Billing\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

/**
 * Class TransparencyTest.
 */
class TransparencyTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The transparency route shall display the transparency screen.
     *
     * @return void
     */
    public function testView()
    {
        $response = $this->get(route('transparency.show'));

        $response->assertViewIs('billing.transparency');
    }

    /**
     * The transparency route shall contain the nullable rule on the date field.
     *
     * @return void
     */
    public function testNullable()
    {
        $response = $this->get(route('transparency.show'));

        $response->assertSessionHasNoErrors();
    }

    /**
     * The transparency route shall contain the date_format rule on the date field.
     *
     * @return void
     */
    public function testDateFormatRule()
    {
        $date = $this->faker->word();

        $response = $this->get(route('transparency.show', ['date' => $date]));

        $response->assertInvalid(['date' => 'The date does not match the format Y-m.']);
    }

    /**
     * The transparency route shall contain the transparency date rule on the date field.
     *
     * @return void
     */
    public function testTransparencyDateRule()
    {
        Balance::factory()->create();

        $date = Date::now()->subMonths($this->faker->randomDigitNotNull())->format(AllowedDateFormat::YM);

        $response = $this->get(route('transparency.show', ['date' => $date]));

        $response->assertInvalid(['date' => 'The selected month is not valid.']);
    }

    /**
     * The transparency route shall bind the selected month's balances.
     *
     * @return void
     */
    public function testBalances()
    {
        Balance::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('transparency.show'));

        $response->assertViewHas('balances');
    }

    /**
     * The transparency route shall bind the selected month's transactions.
     *
     * @return void
     */
    public function testTransactions()
    {
        Transaction::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('transparency.show'));

        $response->assertViewHas('transactions');
    }

    /**
     * The transparency route shall bind the date field options.
     *
     * @return void
     */
    public function testFilterOptions()
    {
        Balance::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('transparency.show'));

        $response->assertViewHas('filterOptions');
    }

    /**
     * The transparency route shall bind the date field selected value.
     *
     * @return void
     */
    public function testSelectedDate()
    {
        Balance::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('transparency.show'));

        $response->assertViewHas('selectedDate');
    }
}
