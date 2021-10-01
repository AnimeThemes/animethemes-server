<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Filters\Base;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Nova\Filters\Base\CreatedEndDateFilter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class CreatedEndDateTest.
 */
class CreatedEndDateTest extends TestCase
{
    use NovaFilterTest;
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Created End Date Filter shall be a date filter.
     *
     * @return void
     *
     * @throws InvalidNovaFilterException
     */
    public function testDateFilter()
    {
        static::novaFilter(CreatedEndDateFilter::class)
            ->assertDateFilter();
    }

    /**
     * The Created End Date Filter shall filter Models By Create Date.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter()
    {
        $dateFilter = Carbon::now()->subDays($this->faker->randomDigitNotNull());

        Carbon::withTestNow(Carbon::now()->subMonths($this->faker->randomDigitNotNull()), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Anime::factory()->count($this->faker->randomDigitNotNull())->create();

        $filter = static::novaFilter(CreatedEndDateFilter::class);

        $response = $filter->apply(Anime::class, $dateFilter);

        $filteredAnimes = Anime::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, ComparisonOperator::LTE, $dateFilter)->get();
        foreach ($filteredAnimes as $filteredAnime) {
            $response->assertContains($filteredAnime);
        }
        $response->assertCount($filteredAnimes->count());
    }
}
