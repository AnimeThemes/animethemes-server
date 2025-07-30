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
use App\Http\Api\Schema\Wiki\GroupSchema;
use App\Http\Resources\Wiki\Resource\GroupResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Group;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $group = Group::factory()->create();

    $response = $this->get(route('api.group.show', ['group' => $group]));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupResource($group, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('soft delete', function () {
    $group = Group::factory()->trashed()->createOne();

    $group->unsetRelations();

    $response = $this->get(route('api.group.show', ['group' => $group]));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupResource($group, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new GroupSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $group = Group::factory()
        ->has(AnimeTheme::factory()->count(fake()->randomDigitNotNull())->for(Anime::factory()))
        ->createOne();

    $response = $this->get(route('api.group.show', ['group' => $group] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupResource($group, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new GroupSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            GroupResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $group = Group::factory()->create();

    $response = $this->get(route('api.group.show', ['group' => $group] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupResource($group, new Query($parameters))
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
        IncludeParser::param() => Group::RELATION_THEMES,
    ];

    $group = Group::factory()
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

    $group->unsetRelations()->load([
        Group::RELATION_THEMES => function (HasMany $query) use ($sequenceFilter) {
            $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
        },
    ]);

    $response = $this->get(route('api.group.show', ['group' => $group] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupResource($group, new Query($parameters))
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
        IncludeParser::param() => Group::RELATION_THEMES,
    ];

    $group = Group::factory()
        ->has(AnimeTheme::factory()->count(fake()->randomDigitNotNull())->for(Anime::factory()))
        ->createOne();

    $group->unsetRelations()->load([
        Group::RELATION_THEMES => function (HasMany $query) use ($typeFilter) {
            $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
        },
    ]);

    $response = $this->get(route('api.group.show', ['group' => $group] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupResource($group, new Query($parameters))
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
        IncludeParser::param() => Group::RELATION_ANIME,
    ];

    $group = Group::factory()
        ->has(AnimeTheme::factory()->count(fake()->randomDigitNotNull())->for(Anime::factory()))
        ->createOne();

    $group->unsetRelations()->load([
        Group::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
            $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
        },
    ]);

    $response = $this->get(route('api.group.show', ['group' => $group] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupResource($group, new Query($parameters))
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
        IncludeParser::param() => Group::RELATION_ANIME,
    ];

    $group = Group::factory()
        ->has(AnimeTheme::factory()->count(fake()->randomDigitNotNull())->for(Anime::factory()))
        ->createOne();

    $group->unsetRelations()->load([
        Group::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ]);

    $response = $this->get(route('api.group.show', ['group' => $group] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupResource($group, new Query($parameters))
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
        IncludeParser::param() => Group::RELATION_ANIME,
    ];

    $group = Group::factory()
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

    $group->unsetRelations()->load([
        Group::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ]);

    $response = $this->get(route('api.group.show', ['group' => $group] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupResource($group, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
