<?php

namespace Tests\Unit\Nova\Filters;

use App\Enums\Filter\ComparisonOperator;
use App\Models\Anime;
use App\Nova\Filters\DeletedStartDateFilter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Http\Request;
use JoshGaber\NovaUnit\Filters\MockFilterQuery;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class DeletedStartDateTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Deleted Start Date Filter shall be a date filter.
     *
     * @return void
     */
    public function testDateFilter()
    {
        $this->novaFilter(DeletedStartDateFilter::class)
            ->assertDateFilter();
    }

    /**
     * The Deleted End Date Filter shall filter Models By Delete Date.
     *
     * @return void
     */
    public function testFilter()
    {
        $date_filter = Carbon::now()->subDays($this->faker->randomDigitNotNull);

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

        $response = new MockFilterQuery((new DeletedStartDateFilter)->apply(Request::createFromGlobals(), Anime::withTrashed(), $date_filter));

        $filtered_animes = Anime::withTrashed()->where('deleted_at', ComparisonOperator::GTE, $date_filter)->get();
        foreach ($filtered_animes as $filtered_anime) {
            $response->assertContains($filtered_anime);
        }
        $response->assertCount($filtered_animes->count());
    }
}
