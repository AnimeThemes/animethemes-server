<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Resources\Wiki\Anime\Theme\Resource\EntryResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->create();

    $response = get(route('api.animethemeentry.show', ['animethemeentry' => $entry]));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryResource($entry, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('soft delete', function () {
    $entry = AnimeThemeEntry::factory()
        ->trashed()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $entry->unsetRelations();

    $response = get(route('api.animethemeentry.show', ['animethemeentry' => $entry]));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryResource($entry, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new EntrySchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        ->createOne();

    $response = get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryResource($entry, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new EntrySchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            EntryResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->create();

    $response = get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryResource($entry, new Query($parameters))
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
        IncludeParser::param() => AnimeThemeEntry::RELATION_ANIME,
    ];

    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $entry->unsetRelations()->load([
        AnimeThemeEntry::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
            $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
        },
    ]);

    $response = get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryResource($entry, new Query($parameters))
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
        IncludeParser::param() => AnimeThemeEntry::RELATION_ANIME,
    ];

    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $entry->unsetRelations()->load([
        AnimeThemeEntry::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ]);

    $response = get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryResource($entry, new Query($parameters))
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
        IncludeParser::param() => AnimeThemeEntry::RELATION_ANIME,
    ];

    $entry = AnimeThemeEntry::factory()
        ->for(
            AnimeTheme::factory()->for(
                Anime::factory()
                    ->state([
                        Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
                    ])
            )
        )
        ->createOne();

    $entry->unsetRelations()->load([
        AnimeThemeEntry::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ]);

    $response = get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryResource($entry, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('themes by sequence', function () {
    $sequenceFilter = fake()->randomDigitNotNull();
    $excludedSequence = $sequenceFilter + 1;

    $parameters = [
        FilterParser::param() => [
            AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
        ],
        IncludeParser::param() => AnimeThemeEntry::RELATION_THEME,
    ];

    $entry = AnimeThemeEntry::factory()
        ->for(
            AnimeTheme::factory()
                ->for(Anime::factory())
                ->state([
                    AnimeTheme::ATTRIBUTE_SEQUENCE => fake()->boolean() ? $sequenceFilter : $excludedSequence,
                ])
        )
        ->createOne();

    $entry->unsetRelations()->load([
        AnimeThemeEntry::RELATION_THEME => function (BelongsTo $query) use ($sequenceFilter) {
            $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
        },
    ]);

    $response = get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryResource($entry, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('themes by type', function () {
    $typeFilter = Arr::random(ThemeType::cases());

    $parameters = [
        FilterParser::param() => [
            AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->localize(),
        ],
        IncludeParser::param() => AnimeThemeEntry::RELATION_THEME,
    ];

    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $entry->unsetRelations()->load([
        AnimeThemeEntry::RELATION_THEME => function (BelongsTo $query) use ($typeFilter) {
            $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
        },
    ]);

    $response = get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryResource($entry, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
