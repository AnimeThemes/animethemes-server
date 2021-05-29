<?php

namespace Tests\Feature\Http\Api\Anime;

use App\Enums\AnimeSeason;
use App\Http\Resources\AnimeCollection;
use App\Http\Resources\AnimeResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class YearShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Year Show Endpoint shall return a grouping of Anime Resources of year by season.
     *
     * @return void
     */
    public function testDefault()
    {
        $this->withoutEvents();

        $year = intval($this->faker->year());

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::WINTER,
            ]);

        $winterAnime = Anime::where('season', AnimeSeason::WINTER)->get();
        $winterResources = AnimeCollection::make($winterAnime->sortBy('name')->values(), QueryParser::make());

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SPRING,
            ]);

        $springAnime = Anime::where('season', AnimeSeason::SPRING)->get();
        $springResources = AnimeCollection::make($springAnime->sortBy('name')->values(), QueryParser::make());

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SUMMER,
            ]);

        $summerAnime = Anime::where('season', AnimeSeason::SUMMER)->get();
        $summerResources = AnimeCollection::make($summerAnime->sortBy('name')->values(), QueryParser::make());

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::FALL,
            ]);

        $fallAnime = Anime::where('season', AnimeSeason::FALL)->get();
        $fallResources = AnimeCollection::make($fallAnime->sortBy('name')->values(), QueryParser::make());

        $response = $this->get(route('api.year.show', ['year' => $year]));

        $response->assertJson([
            Str::lower(AnimeSeason::getDescription(AnimeSeason::WINTER)) => json_decode(json_encode($winterResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::SPRING)) => json_decode(json_encode($springResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::SUMMER)) => json_decode(json_encode($summerResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::FALL)) => json_decode(json_encode($fallResources->response()->getData()->anime), true),
        ]);
    }

    /**
     * The Year Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $year = intval($this->faker->year());

        $allowedPaths = collect(AnimeCollection::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                'year' => $year,
                'season' => AnimeSeason::WINTER,
            ]);

        $winterAnime = Anime::where('season', AnimeSeason::WINTER)->with($includedPaths->all())->get();
        $winterResources = AnimeCollection::make($winterAnime->sortBy('name')->values(), QueryParser::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SPRING,
            ]);

        $springAnime = Anime::where('season', AnimeSeason::SPRING)->with($includedPaths->all())->get();
        $springResources = AnimeCollection::make($springAnime->sortBy('name')->values(), QueryParser::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SUMMER,
            ]);

        $summerAnime = Anime::where('season', AnimeSeason::SUMMER)->with($includedPaths->all())->get();
        $summerResources = AnimeCollection::make($summerAnime->sortBy('name')->values(), QueryParser::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                'year' => $year,
                'season' => AnimeSeason::FALL,
            ]);

        $fallAnime = Anime::where('season', AnimeSeason::FALL)->with($includedPaths->all())->get();
        $fallResources = AnimeCollection::make($fallAnime->sortBy('name')->values(), QueryParser::make($parameters));

        $response = $this->get(route('api.year.show', ['year' => $year] + $parameters));

        $response->assertJson([
            Str::lower(AnimeSeason::getDescription(AnimeSeason::WINTER)) => json_decode(json_encode($winterResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::SPRING)) => json_decode(json_encode($springResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::SUMMER)) => json_decode(json_encode($summerResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::FALL)) => json_decode(json_encode($fallResources->response()->getData()->anime), true),
        ]);
    }

    /**
     * The Year Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $this->withoutEvents();

        $year = intval($this->faker->year());

        $fields = collect([
            'id',
            'name',
            'slug',
            'year',
            'season',
            'synopsis',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                AnimeResource::$wrap => $includedFields->join(','),
            ],
        ];

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::WINTER,
            ]);

        $winterAnime = Anime::where('season', AnimeSeason::WINTER)->get();
        $winterResources = AnimeCollection::make($winterAnime->sortBy('name')->values(), QueryParser::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SPRING,
            ]);

        $springAnime = Anime::where('season', AnimeSeason::SPRING)->get();
        $springResources = AnimeCollection::make($springAnime->sortBy('name')->values(), QueryParser::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SUMMER,
            ]);

        $summerAnime = Anime::where('season', AnimeSeason::SUMMER)->get();
        $summerResources = AnimeCollection::make($summerAnime->sortBy('name')->values(), QueryParser::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::FALL,
            ]);

        $fallAnime = Anime::where('season', AnimeSeason::FALL)->get();
        $fallResources = AnimeCollection::make($fallAnime->sortBy('name')->values(), QueryParser::make($parameters));

        $response = $this->get(route('api.year.show', ['year' => $year]));

        $response->assertJson([
            Str::lower(AnimeSeason::getDescription(AnimeSeason::WINTER)) => json_decode(json_encode($winterResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::SPRING)) => json_decode(json_encode($springResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::SUMMER)) => json_decode(json_encode($summerResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::FALL)) => json_decode(json_encode($fallResources->response()->getData()->anime), true),
        ]);
    }
}
