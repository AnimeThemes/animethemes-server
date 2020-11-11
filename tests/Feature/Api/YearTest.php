<?php

namespace Tests\Feature\Api;

use App\Enums\AnimeSeason;
use App\Models\Anime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class YearTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The Year Index Endpoint shall display a list of unique years of anime
     *
     * @return void
     */
    public function testYearIndexAttributes()
    {
        $anime = Anime::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.year.index'));

        $response->assertJson(
            $anime->unique('year')->sortBy('year')->pluck('year')->all(),
        );
    }

    /**
     * The Year Index Endpoint shall display a listing of anime of year by season
     *
     * @return void
     */
    public function testShowYearAttributes()
    {
        $year = $this->faker->year();

        $fall_anime = Anime::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create([
                'year' => $year,
                'season' => AnimeSeason::FALL,
            ]);

        $summer_anime = Anime::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SUMMER,
            ]);

        $spring_anime = Anime::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SPRING,
            ]);

        $winter_anime = Anime::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create([
                'year' => $year,
                'season' => AnimeSeason::WINTER,
            ]);

        $response = $this->get(route('api.year.show', ['year' => $year]));

        $response->assertJson([
            Str::lower(AnimeSeason::getDescription(AnimeSeason::FALL)) =>
                $fall_anime->sortBy('name')->values()->map(function ($anime) {
                    return AnimeTest::getData($anime);
                })->toArray(),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::SPRING)) =>
                $spring_anime->sortBy('name')->values()->map(function ($anime) {
                    return AnimeTest::getData($anime);
                })->toArray(),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::SUMMER)) =>
                $summer_anime->sortBy('name')->values()->map(function ($anime) {
                    return AnimeTest::getData($anime);
                })->toArray(),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::WINTER)) =>
                $winter_anime->sortBy('name')->values()->map(function ($anime) {
                    return AnimeTest::getData($anime);
                })->toArray(),
        ]);
    }
}
