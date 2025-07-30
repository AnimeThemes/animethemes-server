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
use App\Http\Api\Schema\Pivot\Wiki\AnimeStudioSchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeStudioResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('not found', function () {
    $anime = Anime::factory()->createOne();
    $studio = Studio::factory()->createOne();

    $response = get(route('api.animestudio.show', ['anime' => $anime, 'studio' => $studio]));

    $response->assertNotFound();
});

test('default', function () {
    $animeStudio = AnimeStudio::factory()
        ->for(Anime::factory())
        ->for(Studio::factory())
        ->createOne();

    $response = get(route('api.animestudio.show', ['anime' => $animeStudio->anime, 'studio' => $animeStudio->studio]));

    $animeStudio->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeStudioResource($animeStudio, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new AnimeStudioSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $animeStudio = AnimeStudio::factory()
        ->for(Anime::factory())
        ->for(Studio::factory())
        ->createOne();

    $response = get(route('api.animestudio.show', ['anime' => $animeStudio->anime, 'studio' => $animeStudio->studio] + $parameters));

    $animeStudio->unsetRelations()->load($includedPaths->all());

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeStudioResource($animeStudio, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new AnimeStudioSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            AnimeStudioResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $animeStudio = AnimeStudio::factory()
        ->for(Anime::factory())
        ->for(Studio::factory())
        ->createOne();

    $response = get(route('api.animestudio.show', ['anime' => $animeStudio->anime, 'studio' => $animeStudio->studio] + $parameters));

    $animeStudio->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeStudioResource($animeStudio, new Query($parameters))
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
        IncludeParser::param() => AnimeStudio::RELATION_ANIME,
    ];

    $animeStudio = AnimeStudio::factory()
        ->for(Anime::factory())
        ->for(Studio::factory())
        ->createOne();

    $response = get(route('api.animestudio.show', ['anime' => $animeStudio->anime, 'studio' => $animeStudio->studio] + $parameters));

    $animeStudio->unsetRelations()->load([
        AnimeStudio::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
            $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeStudioResource($animeStudio, new Query($parameters))
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
        IncludeParser::param() => AnimeStudio::RELATION_ANIME,
    ];

    $animeStudio = AnimeStudio::factory()
        ->for(Anime::factory())
        ->for(Studio::factory())
        ->createOne();

    $response = get(route('api.animestudio.show', ['anime' => $animeStudio->anime, 'studio' => $animeStudio->studio] + $parameters));

    $animeStudio->unsetRelations()->load([
        AnimeStudio::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeStudioResource($animeStudio, new Query($parameters))
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
        IncludeParser::param() => AnimeStudio::RELATION_ANIME,
    ];

    $animeStudio = AnimeStudio::factory()
        ->for(
            Anime::factory()
                ->state([
                    Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
                ])
        )
        ->for(Studio::factory())
        ->createOne();

    $response = get(route('api.animestudio.show', ['anime' => $animeStudio->anime, 'studio' => $animeStudio->studio] + $parameters));

    $animeStudio->unsetRelations()->load([
        AnimeStudio::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeStudioResource($animeStudio, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
