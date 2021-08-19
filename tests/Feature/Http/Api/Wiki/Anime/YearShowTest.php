<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class YearShowTest.
 */
class YearShowTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

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

        $winterAnime = Anime::query()->where('season', AnimeSeason::WINTER)->get();
        $winterResources = AnimeCollection::make($winterAnime->sortBy('name')->values(), Query::make());

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SPRING,
            ]);

        $springAnime = Anime::query()->where('season', AnimeSeason::SPRING)->get();
        $springResources = AnimeCollection::make($springAnime->sortBy('name')->values(), Query::make());

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SUMMER,
            ]);

        $summerAnime = Anime::query()->where('season', AnimeSeason::SUMMER)->get();
        $summerResources = AnimeCollection::make($summerAnime->sortBy('name')->values(), Query::make());

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::FALL,
            ]);

        $fallAnime = Anime::query()->where('season', AnimeSeason::FALL)->get();
        $fallResources = AnimeCollection::make($fallAnime->sortBy('name')->values(), Query::make());

        $response = $this->get(route('api.animeyear.show', ['year' => $year]));

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
            IncludeParser::$param => $includedPaths->join(','),
        ];

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                'year' => $year,
                'season' => AnimeSeason::WINTER,
            ]);

        $winterAnime = Anime::query()->where('season', AnimeSeason::WINTER)->with($includedPaths->all())->get();
        $winterResources = AnimeCollection::make($winterAnime->sortBy('name')->values(), Query::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SPRING,
            ]);

        $springAnime = Anime::query()->where('season', AnimeSeason::SPRING)->with($includedPaths->all())->get();
        $springResources = AnimeCollection::make($springAnime->sortBy('name')->values(), Query::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SUMMER,
            ]);

        $summerAnime = Anime::query()->where('season', AnimeSeason::SUMMER)->with($includedPaths->all())->get();
        $summerResources = AnimeCollection::make($summerAnime->sortBy('name')->values(), Query::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                'year' => $year,
                'season' => AnimeSeason::FALL,
            ]);

        $fallAnime = Anime::query()->where('season', AnimeSeason::FALL)->with($includedPaths->all())->get();
        $fallResources = AnimeCollection::make($fallAnime->sortBy('name')->values(), Query::make($parameters));

        $response = $this->get(route('api.animeyear.show', ['year' => $year] + $parameters));

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
            FieldParser::$param => [
                AnimeResource::$wrap => $includedFields->join(','),
            ],
        ];

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::WINTER,
            ]);

        $winterAnime = Anime::query()->where('season', AnimeSeason::WINTER)->get();
        $winterResources = AnimeCollection::make($winterAnime->sortBy('name')->values(), Query::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SPRING,
            ]);

        $springAnime = Anime::query()->where('season', AnimeSeason::SPRING)->get();
        $springResources = AnimeCollection::make($springAnime->sortBy('name')->values(), Query::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SUMMER,
            ]);

        $summerAnime = Anime::query()->where('season', AnimeSeason::SUMMER)->get();
        $summerResources = AnimeCollection::make($summerAnime->sortBy('name')->values(), Query::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::FALL,
            ]);

        $fallAnime = Anime::query()->where('season', AnimeSeason::FALL)->get();
        $fallResources = AnimeCollection::make($fallAnime->sortBy('name')->values(), Query::make($parameters));

        $response = $this->get(route('api.animeyear.show', ['year' => $year]));

        $response->assertJson([
            Str::lower(AnimeSeason::getDescription(AnimeSeason::WINTER)) => json_decode(json_encode($winterResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::SPRING)) => json_decode(json_encode($springResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::SUMMER)) => json_decode(json_encode($summerResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::FALL)) => json_decode(json_encode($fallResources->response()->getData()->anime), true),
        ]);
    }
}
