<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Models\Billing\Balance;
use App\Models\Billing\Transaction;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

/**
 * Class TransparencyTest.
 */
class TransparencyTest extends TestCase
{
    use WithFaker;

    /**
     * The transparency show endpoint shall contain the nullable rule on the date field.
     *
     * @return void
     */
    public function testNullable(): void
    {
        $response = $this->get(route('api.transparency.show'));

        $response->assertSuccessful();
    }

    /**
     * The transparency show endpoint shall contain the date_format rule on the date field.
     *
     * @return void
     */
    public function testDateFormatRule(): void
    {
        $date = $this->faker->word();

        $response = $this->get(route('api.transparency.show', ['date' => $date]));

        $response->assertInvalid(['date' => 'The date does not match the format Y-m.']);
    }

    /**
     * The transparency route shall contain the transparency date rule on the date field.
     *
     * @return void
     */
    public function testTransparencyDateRule(): void
    {
        Balance::factory()->create();

        $date = Date::now()->subMonths($this->faker->randomDigitNotNull())->format(AllowedDateFormat::YM->value);

        $response = $this->get(route('api.transparency.show', ['date' => $date]));

        $response->assertInvalid(['date' => 'The selected month is not valid.']);
    }

    /**
     * The transparency route shall bind the selected month's balances.
     *
     * @return void
     */
    public function testBalances(): void
    {
        $date = $this->faker->dateTime();

        $balanceCount = $this->faker->randomDigitNotNull();

        Balance::factory()->count($balanceCount)->create([
            Balance::ATTRIBUTE_DATE => $date->format(AllowedDateFormat::YMD->value),
        ]);

        $response = $this->get(route('api.transparency.show', ['date' => $date->format(AllowedDateFormat::YM->value)]));

        $response->assertJsonCount($balanceCount, 'transparency.balances');
    }

    /**
     * The transparency route shall bind the selected month's transactions.
     *
     * @return void
     */
    public function testTransactions(): void
    {
        $date = $this->faker->dateTime();

        $balanceCount = $this->faker->randomDigitNotNull();

        Transaction::factory()->count($balanceCount)->create([
            Transaction::ATTRIBUTE_DATE => $date->format(AllowedDateFormat::YMD->value),
        ]);

        $response = $this->get(route('api.transparency.show', ['date' => $date->format(AllowedDateFormat::YM->value)]));

        $response->assertJsonCount($balanceCount, 'transparency.transactions');
    }

    /**
     * The transparency route shall bind the date field options.
     *
     * @return void
     */
    public function testFilterOptions(): void
    {
        $date = $this->faker->dateTime();

        Balance::factory()->create([
            Balance::ATTRIBUTE_DATE => $date->format(AllowedDateFormat::YMD->value),
        ]);

        $response = $this->get(route('api.transparency.show', ['date' => $date->format(AllowedDateFormat::YM->value)]));

        $response->assertJsonCount(1, 'transparency.filterOptions');
    }

    /**
     * The transparency route shall bind the date field selected value.
     *
     * @return void
     */
    public function testSelectedDate(): void
    {
        $date = $this->faker->dateTime();

        Balance::factory()->create([
            Balance::ATTRIBUTE_DATE => $date->format(AllowedDateFormat::YMD->value),
        ]);

        $response = $this->get(route('api.transparency.show', ['date' => $date->format(AllowedDateFormat::YM->value)]));

        $response->assertJsonStructure([
            'transparency' => [
                'selectedDate',
            ],
        ]);
    }
}
