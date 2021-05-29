<?php

namespace Tests\Unit\Nova\Filters;

use App\Enums\Filter\ComparisonOperator;
use App\Models\Anime;
use App\Nova\Filters\CreatedEndDateFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class CreatedEndDateTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Created End Date Filter shall be a date filter.
     *
     * @return void
     */
    public function testDateFilter()
    {
        $this->novaFilter(CreatedEndDateFilter::class)
            ->assertDateFilter();
    }

    /**
     * The Created End Date Filter shall filter Models By Create Date.
     *
     * @return void
     */
    public function testFilter()
    {
        $dateFilter = Carbon::now()->subDays($this->faker->randomDigitNotNull);

        Carbon::withTestNow(Carbon::now()->subMonths($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Anime::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = $this->novaFilter(CreatedEndDateFilter::class);

        $response = $filter->apply(Anime::class, $dateFilter);

        $filteredAnimes = Anime::where(Model::CREATED_AT, ComparisonOperator::LTE, $dateFilter)->get();
        foreach ($filteredAnimes as $filteredAnime) {
            $response->assertContains($filteredAnime);
        }
        $response->assertCount($filteredAnimes->count());
    }
}
