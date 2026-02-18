<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\Anime\AnimeSynonymSchema;
use App\Http\Resources\Wiki\Anime\Resource\AnimeSynonymJsonResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

    $synonym->unsetRelations();

    $response = get(route('api.animesynonym.show', ['animesynonym' => $synonym]));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeSynonymJsonResource($synonym, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('soft delete', function () {
    $synonym = AnimeSynonym::factory()
        ->trashed()
        ->for(Anime::factory())
        ->createOne();

    $synonym->unsetRelations();

    $response = get(route('api.animesynonym.show', ['animesynonym' => $synonym]));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeSynonymJsonResource($synonym, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new AnimeSynonymSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

    $response = get(route('api.animesynonym.show', ['animesynonym' => $synonym] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeSynonymJsonResource($synonym, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new AnimeSynonymSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            AnimeSynonymJsonResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

    $synonym->unsetRelations();

    $response = get(route('api.animesynonym.show', ['animesynonym' => $synonym] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeSynonymJsonResource($synonym, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by media format', function () {
    $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
        ],
        IncludeParser::param() => AnimeSynonym::RELATION_ANIME,
    ];

    $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

    $synonym->unsetRelations()->load([
        AnimeSynonym::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
            $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
        },
    ]);

    $response = get(route('api.animesynonym.show', ['animesynonym' => $synonym] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeSynonymJsonResource($synonym, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by season', function () {
    $seasonFilter = Arr::random(AnimeSeason::cases());

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
        ],
        IncludeParser::param() => AnimeSynonym::RELATION_ANIME,
    ];

    $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

    $synonym->unsetRelations()->load([
        AnimeSynonym::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ]);

    $response = get(route('api.animesynonym.show', ['animesynonym' => $synonym] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeSynonymJsonResource($synonym, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by year', function () {
    $yearFilter = intval(fake()->year());
    $excludedYear = $yearFilter + 1;

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_YEAR => $yearFilter,
        ],
        IncludeParser::param() => AnimeSynonym::RELATION_ANIME,
    ];

    $synonym = AnimeSynonym::factory()
        ->for(
            Anime::factory()
                ->state([
                    Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
                ])
        )
        ->createOne();

    $synonym->unsetRelations()->load([
        AnimeSynonym::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ]);

    $response = get(route('api.animesynonym.show', ['animesynonym' => $synonym] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeSynonymJsonResource($synonym, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
