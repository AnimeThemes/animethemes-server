<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class YearShowTest.
 */
class YearShowTest extends TestCase
{
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
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::WINTER,
            ]);

        $winterAnime = Anime::query()->where(Anime::ATTRIBUTE_SEASON, AnimeSeason::WINTER)->get();
        $winterResources = AnimeCollection::make($winterAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), Query::make());

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::SPRING,
            ]);

        $springAnime = Anime::query()->where(Anime::ATTRIBUTE_SEASON, AnimeSeason::SPRING)->get();
        $springResources = AnimeCollection::make($springAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), Query::make());

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::SUMMER,
            ]);

        $summerAnime = Anime::query()->where(Anime::ATTRIBUTE_SEASON, AnimeSeason::SUMMER)->get();
        $summerResources = AnimeCollection::make($summerAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), Query::make());

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::FALL,
            ]);

        $fallAnime = Anime::query()->where(Anime::ATTRIBUTE_SEASON, AnimeSeason::FALL)->get();
        $fallResources = AnimeCollection::make($fallAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), Query::make());

        $response = $this->get(route('api.animeyear.show', [Anime::ATTRIBUTE_YEAR => $year]));

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

        $schema = new AnimeSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::$param => $includedPaths->join(','),
        ];

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::WINTER,
            ]);

        $winterAnime = Anime::query()->where(Anime::ATTRIBUTE_SEASON, AnimeSeason::WINTER)->with($includedPaths->all())->get();
        $winterResources = AnimeCollection::make($winterAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), Query::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::SPRING,
            ]);

        $springAnime = Anime::query()->where(Anime::ATTRIBUTE_SEASON, AnimeSeason::SPRING)->with($includedPaths->all())->get();
        $springResources = AnimeCollection::make($springAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), Query::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::SUMMER,
            ]);

        $summerAnime = Anime::query()->where(Anime::ATTRIBUTE_SEASON, AnimeSeason::SUMMER)->with($includedPaths->all())->get();
        $summerResources = AnimeCollection::make($summerAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), Query::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::FALL,
            ]);

        $fallAnime = Anime::query()->where(Anime::ATTRIBUTE_SEASON, AnimeSeason::FALL)->with($includedPaths->all())->get();
        $fallResources = AnimeCollection::make($fallAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), Query::make($parameters));

        $response = $this->get(route('api.animeyear.show', [Anime::ATTRIBUTE_YEAR => $year] + $parameters));

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

        $schema = new AnimeSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::$param => [
                AnimeResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::WINTER,
            ]);

        $winterAnime = Anime::query()->where(Anime::ATTRIBUTE_SEASON, AnimeSeason::WINTER)->get();
        $winterResources = AnimeCollection::make($winterAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), Query::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::SPRING,
            ]);

        $springAnime = Anime::query()->where(Anime::ATTRIBUTE_SEASON, AnimeSeason::SPRING)->get();
        $springResources = AnimeCollection::make($springAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), Query::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::SUMMER,
            ]);

        $summerAnime = Anime::query()->where(Anime::ATTRIBUTE_SEASON, AnimeSeason::SUMMER)->get();
        $summerResources = AnimeCollection::make($summerAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), Query::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::FALL,
            ]);

        $fallAnime = Anime::query()->where(Anime::ATTRIBUTE_SEASON, AnimeSeason::FALL)->get();
        $fallResources = AnimeCollection::make($fallAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), Query::make($parameters));

        $response = $this->get(route('api.animeyear.show', [Anime::ATTRIBUTE_YEAR => $year]));

        $response->assertJson([
            Str::lower(AnimeSeason::getDescription(AnimeSeason::WINTER)) => json_decode(json_encode($winterResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::SPRING)) => json_decode(json_encode($springResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::SUMMER)) => json_decode(json_encode($summerResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::FALL)) => json_decode(json_encode($fallResources->response()->getData()->anime), true),
        ]);
    }
}
