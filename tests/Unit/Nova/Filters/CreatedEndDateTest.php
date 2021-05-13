<?php

namespace Tests\Unit\Nova\Filters;

use App\Enums\Filter\AllowedDateFormat;
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
        $date_filter = Carbon::now()->subDays($this->faker->randomDigitNotNull);

        Carbon::withTestNow(Carbon::now()->subMonths($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Anime::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = $this->novaFilter(CreatedEndDateFilter::class);

        $response = $filter->apply(Anime::class, $date_filter);

        $filtered_animes = Anime::where(Model::CREATED_AT, ComparisonOperator::LTE, $date_filter)->get();
        foreach ($filtered_animes as $filtered_anime) {
            $response->assertContains($filtered_anime);
        }
        $response->assertCount($filtered_animes->count());
    }
}
