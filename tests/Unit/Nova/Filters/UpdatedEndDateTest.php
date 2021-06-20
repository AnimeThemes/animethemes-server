<?php

declare(strict_types=1);

namespace Nova\Filters;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Models\Wiki\Anime;
use App\Nova\Filters\UpdatedEndDateFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class UpdatedEndDateTest.
 */
class UpdatedEndDateTest extends TestCase
{
    use NovaFilterTest;
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Updated End Date Filter shall be a date filter.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testDateFilter()
    {
        static::novaFilter(UpdatedEndDateFilter::class)
            ->assertDateFilter();
    }

    /**
     * The Updated End Date Filter shall filter Models By Update Date.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter()
    {
        $dateFilter = Carbon::now()->subDays($this->faker->randomDigitNotNull);

        Carbon::withTestNow(Carbon::now()->subMonths($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Anime::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = static::novaFilter(UpdatedEndDateFilter::class);

        $response = $filter->apply(Anime::class, $dateFilter);

        $filteredAnimes = Anime::where(Model::UPDATED_AT, ComparisonOperator::LTE, $dateFilter)->get();
        foreach ($filteredAnimes as $filteredAnime) {
            $response->assertContains($filteredAnime);
        }
        $response->assertCount($filteredAnimes->count());
    }
}
