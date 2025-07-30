<?php

declare(strict_types=1);

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
use Illuminate\Support\Str;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $year = intval(fake()->year());

    $winterAnime = Anime::factory()
        ->count(fake()->numberBetween(1, 3))
        ->create([
            Anime::ATTRIBUTE_YEAR => $year,
            Anime::ATTRIBUTE_SEASON => AnimeSeason::WINTER->value,
        ]);

    $winterResources = new AnimeCollection($winterAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query());

    $springAnime = Anime::factory()
        ->count(fake()->numberBetween(1, 3))
        ->create([
            Anime::ATTRIBUTE_YEAR => $year,
            Anime::ATTRIBUTE_SEASON => AnimeSeason::SPRING->value,
        ]);

    $springResources = new AnimeCollection($springAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query());

    $summerAnime = Anime::factory()
        ->count(fake()->numberBetween(1, 3))
        ->create([
            Anime::ATTRIBUTE_YEAR => $year,
            Anime::ATTRIBUTE_SEASON => AnimeSeason::SUMMER->value,
        ]);

    $summerResources = new AnimeCollection($summerAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query());

    $fallAnime = Anime::factory()
        ->count(fake()->numberBetween(1, 3))
        ->create([
            Anime::ATTRIBUTE_YEAR => $year,
            Anime::ATTRIBUTE_SEASON => AnimeSeason::FALL->value,
        ]);

    $fallResources = new AnimeCollection($fallAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query());

    $response = get(route('api.animeyear.show', [Anime::ATTRIBUTE_YEAR => $year]));

    $response->assertJson([
        Str::lower(AnimeSeason::WINTER->localize()) => json_decode(json_encode($winterResources->response()->getData()->anime), true),
        Str::lower(AnimeSeason::SPRING->localize()) => json_decode(json_encode($springResources->response()->getData()->anime), true),
        Str::lower(AnimeSeason::SUMMER->localize()) => json_decode(json_encode($summerResources->response()->getData()->anime), true),
        Str::lower(AnimeSeason::FALL->localize()) => json_decode(json_encode($fallResources->response()->getData()->anime), true),
    ]);
});

test('allowed include paths', function () {
    $year = intval(fake()->year());

    $schema = new AnimeSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $winterAnime = Anime::factory()
        ->count(fake()->numberBetween(1, 3))
        ->jsonApiResource()
        ->create([
            Anime::ATTRIBUTE_YEAR => $year,
            Anime::ATTRIBUTE_SEASON => AnimeSeason::WINTER->value,
        ]);

    $winterResources = new AnimeCollection($winterAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query($parameters));

    $springAnime = Anime::factory()
        ->count(fake()->numberBetween(1, 3))
        ->jsonApiResource()
        ->create([
            Anime::ATTRIBUTE_YEAR => $year,
            Anime::ATTRIBUTE_SEASON => AnimeSeason::SPRING->value,
        ]);

    $springResources = new AnimeCollection($springAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query($parameters));

    $summerAnime = Anime::factory()
        ->count(fake()->numberBetween(1, 3))
        ->jsonApiResource()
        ->create([
            Anime::ATTRIBUTE_YEAR => $year,
            Anime::ATTRIBUTE_SEASON => AnimeSeason::SUMMER->value,
        ]);

    $summerResources = new AnimeCollection($summerAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query($parameters));

    $fallAnime = Anime::factory()
        ->count(fake()->numberBetween(1, 3))
        ->jsonApiResource()
        ->create([
            Anime::ATTRIBUTE_YEAR => $year,
            Anime::ATTRIBUTE_SEASON => AnimeSeason::FALL->value,
        ]);

    $fallResources = new AnimeCollection($fallAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query($parameters));

    $response = get(route('api.animeyear.show', [Anime::ATTRIBUTE_YEAR => $year] + $parameters));

    $response->assertJson([
        Str::lower(AnimeSeason::WINTER->localize()) => json_decode(json_encode($winterResources->response()->getData()->anime), true),
        Str::lower(AnimeSeason::SPRING->localize()) => json_decode(json_encode($springResources->response()->getData()->anime), true),
        Str::lower(AnimeSeason::SUMMER->localize()) => json_decode(json_encode($summerResources->response()->getData()->anime), true),
        Str::lower(AnimeSeason::FALL->localize()) => json_decode(json_encode($fallResources->response()->getData()->anime), true),
    ]);
});

test('sparse fieldsets', function () {
    $year = intval(fake()->year());

    $schema = new AnimeSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            AnimeResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $winterAnime = Anime::factory()
        ->count(fake()->numberBetween(1, 3))
        ->create([
            Anime::ATTRIBUTE_YEAR => $year,
            Anime::ATTRIBUTE_SEASON => AnimeSeason::WINTER->value,
        ]);

    $winterResources = new AnimeCollection($winterAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query($parameters));

    $springAnime = Anime::factory()
        ->count(fake()->numberBetween(1, 3))
        ->create([
            Anime::ATTRIBUTE_YEAR => $year,
            Anime::ATTRIBUTE_SEASON => AnimeSeason::SPRING->value,
        ]);

    $springResources = new AnimeCollection($springAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query($parameters));

    $summerAnime = Anime::factory()
        ->count(fake()->numberBetween(1, 3))
        ->create([
            Anime::ATTRIBUTE_YEAR => $year,
            Anime::ATTRIBUTE_SEASON => AnimeSeason::SUMMER->value,
        ]);

    $summerResources = new AnimeCollection($summerAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query($parameters));

    $fallAnime = Anime::factory()
        ->count(fake()->numberBetween(1, 3))
        ->create([
            Anime::ATTRIBUTE_YEAR => $year,
            Anime::ATTRIBUTE_SEASON => AnimeSeason::FALL->value,
        ]);

    $fallResources = new AnimeCollection($fallAnime->sortBy(Anime::ATTRIBUTE_NAME)->values(), new Query($parameters));

    $response = get(route('api.animeyear.show', [Anime::ATTRIBUTE_YEAR => $year] + $parameters));

    $response->assertJson([
        Str::lower(AnimeSeason::WINTER->localize()) => json_decode(json_encode($winterResources->response()->getData()->anime), true),
        Str::lower(AnimeSeason::SPRING->localize()) => json_decode(json_encode($springResources->response()->getData()->anime), true),
        Str::lower(AnimeSeason::SUMMER->localize()) => json_decode(json_encode($summerResources->response()->getData()->anime), true),
        Str::lower(AnimeSeason::FALL->localize()) => json_decode(json_encode($fallResources->response()->getData()->anime), true),
    ]);
});
