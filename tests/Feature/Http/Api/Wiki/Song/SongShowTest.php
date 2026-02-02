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
use App\Http\Api\Schema\Wiki\SongSchema;
use App\Http\Resources\Wiki\Resource\SongJsonResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $song = Song::factory()->create();

    $response = get(route('api.song.show', ['song' => $song]));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongJsonResource($song, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('soft delete', function () {
    $song = Song::factory()->trashed()->createOne();

    $song->unsetRelations();

    $response = get(route('api.song.show', ['song' => $song]));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongJsonResource($song, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new SongSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $song = Song::factory()
        ->has(AnimeTheme::factory()->count(fake()->randomDigitNotNull())->for(Anime::factory()))
        ->has(Artist::factory()->count(fake()->randomDigitNotNull()))
        ->createOne();

    $response = get(route('api.song.show', ['song' => $song] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongJsonResource($song, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new SongSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            SongJsonResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $song = Song::factory()->create();

    $response = get(route('api.song.show', ['song' => $song] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongJsonResource($song, new Query($parameters))
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
        IncludeParser::param() => Song::RELATION_ANIMETHEMES,
    ];

    $song = Song::factory()
        ->has(
            AnimeTheme::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(Anime::factory())
                ->state(new Sequence(
                    [AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter],
                    [AnimeTheme::ATTRIBUTE_SEQUENCE => $excludedSequence],
                ))
        )
        ->createOne();

    $song->unsetRelations()->load([
        Song::RELATION_ANIMETHEMES => function (HasMany $query) use ($sequenceFilter) {
            $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
        },
    ]);

    $response = get(route('api.song.show', ['song' => $song] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongJsonResource($song, new Query($parameters))
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
        IncludeParser::param() => Song::RELATION_ANIMETHEMES,
    ];

    $song = Song::factory()
        ->has(AnimeTheme::factory()->count(fake()->randomDigitNotNull())->for(Anime::factory()))
        ->createOne();

    $song->unsetRelations()->load([
        Song::RELATION_ANIMETHEMES => function (HasMany $query) use ($typeFilter) {
            $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
        },
    ]);

    $response = get(route('api.song.show', ['song' => $song] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongJsonResource($song, new Query($parameters))
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
        IncludeParser::param() => Song::RELATION_ANIME,
    ];

    $song = Song::factory()
        ->has(AnimeTheme::factory()->count(fake()->randomDigitNotNull())->for(Anime::factory()))
        ->createOne();

    $song->unsetRelations()->load([
        Song::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
            $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
        },
    ]);

    $response = get(route('api.song.show', ['song' => $song] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongJsonResource($song, new Query($parameters))
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
        IncludeParser::param() => Song::RELATION_ANIME,
    ];

    $song = Song::factory()
        ->has(AnimeTheme::factory()->count(fake()->randomDigitNotNull())->for(Anime::factory()))
        ->createOne();

    $song->unsetRelations()->load([
        Song::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ]);

    $response = get(route('api.song.show', ['song' => $song] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongJsonResource($song, new Query($parameters))
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
        IncludeParser::param() => Song::RELATION_ANIME,
    ];

    $song = Song::factory()
        ->has(
            AnimeTheme::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(
                    Anime::factory()
                        ->state([
                            Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
                        ])
                )
        )
        ->createOne();

    $song->unsetRelations()->load([
        Song::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ]);

    $response = get(route('api.song.show', ['song' => $song] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongJsonResource($song, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
