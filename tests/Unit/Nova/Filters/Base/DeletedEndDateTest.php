<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Filters\Base;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Nova\Filters\Base\DeletedEndDateFilter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Http\Request;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\MockFilterQuery;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class DeletedEndDateTest.
 */
class DeletedEndDateTest extends TestCase
{
    use NovaFilterTest;
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Deleted End Date Filter shall be a date filter.
     *
     * @return void
     *
     * @throws InvalidNovaFilterException
     */
    public function testDateFilter()
    {
        static::novaFilter(DeletedEndDateFilter::class)
            ->assertDateFilter();
    }

    /**
     * The Deleted End Date Filter shall filter Models By Delete Date.
     *
     * @return void
     */
    public function testFilter()
    {
        $dateFilter = Carbon::now()->subDays($this->faker->randomDigitNotNull());

        Carbon::withTestNow(Carbon::now()->subMonths($this->faker->randomDigitNotNull()), function () {
            $anime = Anime::factory()->count($this->faker->randomDigitNotNull())->create();
            $anime->each(function (Anime $item) {
                $item->delete();
            });
        });

        $anime = Anime::factory()->count($this->faker->randomDigitNotNull())->create();
        $anime->each(function (Anime $item) {
            $item->delete();
        });

        $response = new MockFilterQuery(
            (new DeletedEndDateFilter())->apply(
                Request::createFromGlobals(),
                Anime::withTrashed(),
                $dateFilter
            )
        );

        $filteredAnimes = Anime::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, ComparisonOperator::LTE, $dateFilter)->get();
        foreach ($filteredAnimes as $filteredAnime) {
            $response->assertContains($filteredAnime);
        }
        $response->assertCount($filteredAnimes->count());
    }
}
