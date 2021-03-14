<?php

namespace Tests\Unit\Nova\Filters;

use App\Models\Anime;
use App\Nova\Filters\RecentlyUpdatedFilter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class RecentlyUpdatedTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Recently Updated Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(RecentlyUpdatedFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Recently Updated Filter shall have Today, Yesterday, Week, Month and Year Options.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(RecentlyUpdatedFilter::class);

        $filter->assertHasOption(__('nova.today'));
        $filter->assertHasOption(__('nova.yesterday'));
        $filter->assertHasOption(__('nova.this_week'));
        $filter->assertHasOption(__('nova.this_month'));
        $filter->assertHasOption(__('nova.this_year'));
    }

    /**
     * The Recently Updated Filter shall filter models by Updated Date.
     *
     * @return void
     */
    public function testTodayFilter()
    {
        Carbon::withTestNow(Carbon::now()->startOfYear()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::now()->startOfMonth()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::now()->startOfWeek()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::now()->yesterday()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Anime::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = $this->novaFilter(RecentlyUpdatedFilter::class);

        $response = $filter->apply(Anime::class, RecentlyUpdatedFilter::TODAY);

        $filtered_animes = Anime::whereDate('updated_at', Carbon::now())->get();
        foreach ($filtered_animes as $filtered_anime) {
            $response->assertContains($filtered_anime);
        }
        $response->assertCount($filtered_animes->count());
    }

    /**
     * The Recently Updated Filter shall filter models by Updated Date.
     *
     * @return void
     */
    public function testYesterdayFilter()
    {
        Carbon::withTestNow(Carbon::now()->startOfYear()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::now()->startOfMonth()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::now()->startOfWeek()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::now()->yesterday()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Anime::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = $this->novaFilter(RecentlyUpdatedFilter::class);

        $response = $filter->apply(Anime::class, RecentlyUpdatedFilter::YESTERDAY);

        $filtered_animes = Anime::whereDate('updated_at', '>=', Carbon::now()->yesterday())->get();
        foreach ($filtered_animes as $filtered_anime) {
            $response->assertContains($filtered_anime);
        }
        $response->assertCount($filtered_animes->count());
    }

    /**
     * The Recently Updated Filter shall filter models by Updated Date.
     *
     * @return void
     */
    public function testWeekFilter()
    {
        Carbon::withTestNow(Carbon::now()->startOfYear()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::now()->startOfMonth()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::now()->startOfWeek()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::now()->yesterday()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Anime::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = $this->novaFilter(RecentlyUpdatedFilter::class);

        $response = $filter->apply(Anime::class, RecentlyUpdatedFilter::WEEK);

        $filtered_animes = Anime::whereDate('updated_at', '>=', Carbon::now()->startOfWeek())->get();
        foreach ($filtered_animes as $filtered_anime) {
            $response->assertContains($filtered_anime);
        }
        $response->assertCount($filtered_animes->count());
    }

    /**
     * The Recently Updated Filter shall filter models by Updated Date.
     *
     * @return void
     */
    public function testMonthFilter()
    {
        Carbon::withTestNow(Carbon::now()->startOfYear()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::now()->startOfMonth()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::now()->startOfWeek()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::now()->yesterday()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Anime::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = $this->novaFilter(RecentlyUpdatedFilter::class);

        $response = $filter->apply(Anime::class, RecentlyUpdatedFilter::MONTH);

        $filtered_animes = Anime::whereDate('updated_at', '>=', Carbon::now()->startOfMonth())->get();
        foreach ($filtered_animes as $filtered_anime) {
            $response->assertContains($filtered_anime);
        }
        $response->assertCount($filtered_animes->count());
    }

    /**
     * The Recently Updated Filter shall filter models by Updated Date.
     *
     * @return void
     */
    public function testYearFilter()
    {
        Carbon::withTestNow(Carbon::now()->startOfYear()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::now()->startOfMonth()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::now()->startOfWeek()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::now()->yesterday()->addMinutes($this->faker->randomDigitNotNull), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Anime::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = $this->novaFilter(RecentlyUpdatedFilter::class);

        $response = $filter->apply(Anime::class, RecentlyUpdatedFilter::YEAR);

        $filtered_animes = Anime::whereDate('updated_at', '>=', Carbon::now()->startOfYear())->get();
        foreach ($filtered_animes as $filtered_anime) {
            $response->assertContains($filtered_anime);
        }
        $response->assertCount($filtered_animes->count());
    }
}
