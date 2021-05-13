<?php

namespace Tests\Unit\Nova\Filters;

use App\Enums\Filter\ComparisonOperator;
use App\Models\Anime;
use App\Nova\Filters\UpdatedStartDateFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class UpdatedStartDateTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Updated Start Date Filter shall be a date filter.
     *
     * @return void
     */
    public function testDateFilter()
    {
        $this->novaFilter(UpdatedStartDateFilter::class)
            ->assertDateFilter();
    }

    /**
     * The Updated Start Date Filter shall filter Models By Update Date.
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

        $filter = $this->novaFilter(UpdatedStartDateFilter::class);

        $response = $filter->apply(Anime::class, $date_filter);

        $filtered_animes = Anime::where(Model::UPDATED_AT, ComparisonOperator::GTE, $date_filter)->get();
        foreach ($filtered_animes as $filtered_anime) {
            $response->assertContains($filtered_anime);
        }
        $response->assertCount($filtered_animes->count());
    }
}
