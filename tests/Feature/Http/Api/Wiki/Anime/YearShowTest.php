<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class YearShowTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Year Show Endpoint shall return a grouping of Anime Resources of year by season.
     */
    public function testDefault(): void
    {
        $year = intval($this->faker->year());

        $winterAnime = Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::WINTER->value,
            ]);

        $winterResources = new AnimeCollection($winterAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query());

        $springAnime = Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::SPRING->value,
            ]);

        $springResources = new AnimeCollection($springAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query());

        $summerAnime = Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::SUMMER->value,
            ]);

        $summerResources = new AnimeCollection($summerAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query());

        $fallAnime = Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::FALL->value,
            ]);

        $fallResources = new AnimeCollection($fallAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query());

        $response = $this->get(route('api.animeyear.show', [Anime::ATTRIBUTE_YEAR => $year]));

        $response->assertJson([
            Str::lower(AnimeSeason::WINTER->localize()) => json_decode(json_encode($winterResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::SPRING->localize()) => json_decode(json_encode($springResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::SUMMER->localize()) => json_decode(json_encode($summerResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::FALL->localize()) => json_decode(json_encode($fallResources->response()->getData()->anime), true),
        ]);
    }

    /**
     * The Year Show Endpoint shall allow inclusion of related resources.
     */
    public function testAllowedIncludePaths(): void
    {
        $year = intval($this->faker->year());

        $schema = new AnimeSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $winterAnime = Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::WINTER->value,
            ]);

        $winterResources = new AnimeCollection($winterAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query($parameters));

        $springAnime = Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::SPRING->value,
            ]);

        $springResources = new AnimeCollection($springAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query($parameters));

        $summerAnime = Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::SUMMER->value,
            ]);

        $summerResources = new AnimeCollection($summerAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query($parameters));

        $fallAnime = Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::FALL->value,
            ]);

        $fallResources = new AnimeCollection($fallAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query($parameters));

        $response = $this->get(route('api.animeyear.show', [Anime::ATTRIBUTE_YEAR => $year] + $parameters));

        $response->assertJson([
            Str::lower(AnimeSeason::WINTER->localize()) => json_decode(json_encode($winterResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::SPRING->localize()) => json_decode(json_encode($springResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::SUMMER->localize()) => json_decode(json_encode($summerResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::FALL->localize()) => json_decode(json_encode($fallResources->response()->getData()->anime), true),
        ]);
    }

    /**
     * The Year Show Endpoint shall implement sparse fieldsets.
     */
    public function testSparseFieldsets(): void
    {
        $year = intval($this->faker->year());

        $schema = new AnimeSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                AnimeResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $winterAnime = Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::WINTER->value,
            ]);

        $winterResources = new AnimeCollection($winterAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query($parameters));

        $springAnime = Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::SPRING->value,
            ]);

        $springResources = new AnimeCollection($springAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query($parameters));

        $summerAnime = Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::SUMMER->value,
            ]);

        $summerResources = new AnimeCollection($summerAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query($parameters));

        $fallAnime = Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                Anime::ATTRIBUTE_YEAR => $year,
                Anime::ATTRIBUTE_SEASON => AnimeSeason::FALL->value,
            ]);

        $fallResources = new AnimeCollection($fallAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query($parameters));

        $response = $this->get(route('api.animeyear.show', [Anime::ATTRIBUTE_YEAR => $year] + $parameters));

        $response->assertJson([
            Str::lower(AnimeSeason::WINTER->localize()) => json_decode(json_encode($winterResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::SPRING->localize()) => json_decode(json_encode($springResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::SUMMER->localize()) => json_decode(json_encode($summerResources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::FALL->localize()) => json_decode(json_encode($fallResources->response()->getData()->anime), true),
        ]);
    }
}
