<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\AnimeFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\EntrySchema;
use App\Http\Resources\Wiki\Resource\EntryJsonResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;

use function Pest\Laravel\get;

uses(WithFaker::class);

test('default', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->create();

    $response = get(route('api.animethemeentry.show', ['animethemeentry' => $entry]));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryJsonResource($entry, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('soft delete', function (): void {
    $entry = Entry::factory()
        ->trashed()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    $entry->unsetRelations();

    $response = get(route('api.animethemeentry.show', ['animethemeentry' => $entry]));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryJsonResource($entry, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function (): void {
    $schema = new EntrySchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include): string => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        ->createOne();

    $response = get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryJsonResource($entry, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function (): void {
    $schema = new EntrySchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            EntryJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->create();

    $response = get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryJsonResource($entry, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by media format', function (): void {
    $mediaFormatFilter = Arr::random(AnimeFormat::cases());

    $parameters = [
        FilterParser::param() => [
            'media_format' => $mediaFormatFilter->localize(),
        ],
        IncludeParser::param() => Entry::RELATION_ANIME,
    ];

    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    $entry->unsetRelations()->load([
        Entry::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter): void {
            $query->where(Anime::ATTRIBUTE_FORMAT, $mediaFormatFilter->value);
        },
    ]);

    $response = get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryJsonResource($entry, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by season', function (): void {
    $seasonFilter = Arr::random(AnimeSeason::cases());

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
        ],
        IncludeParser::param() => Entry::RELATION_ANIME,
    ];

    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    $entry->unsetRelations()->load([
        Entry::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter): void {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ]);

    $response = get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryJsonResource($entry, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by year', function (): void {
    $yearFilter = intval(fake()->year());
    $excludedYear = $yearFilter + 1;

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_YEAR => $yearFilter,
        ],
        IncludeParser::param() => Entry::RELATION_ANIME,
    ];

    $entry = Entry::factory()
        ->for(
            Theme::factory()->for(
                Anime::factory()
                    ->state([
                        Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
                    ])
            )
        )
        ->createOne();

    $entry->unsetRelations()->load([
        Entry::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter): void {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ]);

    $response = get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryJsonResource($entry, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('themes by sequence', function (): void {
    $sequenceFilter = fake()->randomDigitNotNull();
    $excludedSequence = $sequenceFilter + 1;

    $parameters = [
        FilterParser::param() => [
            Theme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
        ],
        IncludeParser::param() => Entry::RELATION_THEME,
    ];

    $entry = Entry::factory()
        ->for(
            Theme::factory()
                ->for(Anime::factory())
                ->state([
                    Theme::ATTRIBUTE_SEQUENCE => fake()->boolean() ? $sequenceFilter : $excludedSequence,
                ])
        )
        ->createOne();

    $entry->unsetRelations()->load([
        Entry::RELATION_THEME => function (BelongsTo $query) use ($sequenceFilter): void {
            $query->where(Theme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
        },
    ]);

    $response = get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryJsonResource($entry, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('themes by type', function (): void {
    $typeFilter = Arr::random(ThemeType::cases());

    $parameters = [
        FilterParser::param() => [
            Theme::ATTRIBUTE_TYPE => $typeFilter->localize(),
        ],
        IncludeParser::param() => Entry::RELATION_THEME,
    ];

    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    $entry->unsetRelations()->load([
        Entry::RELATION_THEME => function (BelongsTo $query) use ($typeFilter): void {
            $query->where(Theme::ATTRIBUTE_TYPE, $typeFilter->value);
        },
    ]);

    $response = get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryJsonResource($entry, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
