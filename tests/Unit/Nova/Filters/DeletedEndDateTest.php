<?php

namespace Tests\Unit\Nova\Filters;

use App\Enums\Filter\ComparisonOperator;
use App\Models\Anime;
use App\Nova\Filters\DeletedEndDateFilter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Http\Request;
use JoshGaber\NovaUnit\Filters\MockFilterQuery;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class DeletedEndDateTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Deleted End Date Filter shall be a date filter.
     *
     * @return void
     */
    public function testDateFilter()
    {
        $this->novaFilter(DeletedEndDateFilter::class)
            ->assertDateFilter();
    }

    /**
     * The Deleted End Date Filter shall filter Models By Delete Date.
     *
     * @return void
     */
    public function testFilter()
    {
        $dateFilter = Carbon::now()->subDays($this->faker->randomDigitNotNull);

        Carbon::withTestNow(Carbon::now()->subMonths($this->faker->randomDigitNotNull), function () {
            $anime = Anime::factory()->count($this->faker->randomDigitNotNull)->create();
            $anime->each(function ($item) {
                $item->delete();
            });
        });

        $anime = Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        $anime->each(function ($item) {
            $item->delete();
        });

        $response = new MockFilterQuery((new DeletedEndDateFilter)->apply(Request::createFromGlobals(), Anime::withTrashed(), $dateFilter));

        $filteredAnimes = Anime::withTrashed()->where('deleted_at', ComparisonOperator::LTE, $dateFilter)->get();
        foreach ($filteredAnimes as $filteredAnime) {
            $response->assertContains($filteredAnime);
        }
        $response->assertCount($filteredAnimes->count());
    }
}
