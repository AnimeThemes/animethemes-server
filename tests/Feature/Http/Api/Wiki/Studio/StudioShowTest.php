<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Resources\Wiki\Resource\StudioJsonResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $studio = Studio::factory()->create();

    $response = get(route('api.studio.show', ['studio' => $studio]));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioJsonResource($studio, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('soft delete', function () {
    $studio = Studio::factory()->trashed()->createOne();

    $studio->unsetRelations();

    $response = get(route('api.studio.show', ['studio' => $studio]));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioJsonResource($studio, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new StudioSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $studio = Studio::factory()
        ->has(Anime::factory()->count(fake()->randomDigitNotNull()))
        ->createOne();

    $response = get(route('api.studio.show', ['studio' => $studio] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioJsonResource($studio, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new StudioSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            StudioJsonResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $studio = Studio::factory()->create();

    $response = get(route('api.studio.show', ['studio' => $studio] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioJsonResource($studio, new Query($parameters))
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
        IncludeParser::param() => Studio::RELATION_ANIME,
    ];

    $studio = Studio::factory()
        ->has(Anime::factory()->count(fake()->randomDigitNotNull()))
        ->create();

    $studio->unsetRelations()->load([
        Studio::RELATION_ANIME => function (BelongsToMany $query) use ($mediaFormatFilter) {
            $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
        },
    ]);

    $response = get(route('api.studio.show', ['studio' => $studio] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioJsonResource($studio, new Query($parameters))
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
        IncludeParser::param() => Studio::RELATION_ANIME,
    ];

    $studio = Studio::factory()
        ->has(Anime::factory()->count(fake()->randomDigitNotNull()))
        ->create();

    $studio->unsetRelations()->load([
        Studio::RELATION_ANIME => function (BelongsToMany $query) use ($seasonFilter) {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ]);

    $response = get(route('api.studio.show', ['studio' => $studio] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioJsonResource($studio, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by year', function () {
    $yearFilter = fake()->numberBetween(2000, 2002);

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_YEAR => $yearFilter,
        ],
        IncludeParser::param() => Studio::RELATION_ANIME,
    ];

    $studio = Studio::factory()
        ->has(
            Anime::factory()
                ->count(fake()->randomDigitNotNull())
                ->state(new Sequence(
                    [Anime::ATTRIBUTE_YEAR => 2000],
                    [Anime::ATTRIBUTE_YEAR => 2001],
                    [Anime::ATTRIBUTE_YEAR => 2002],
                ))
        )
        ->createOne();

    $studio->unsetRelations()->load([
        Studio::RELATION_ANIME => function (BelongsToMany $query) use ($yearFilter) {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ]);

    $response = get(route('api.studio.show', ['studio' => $studio] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioJsonResource($studio, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('resources by site', function () {
    $siteFilter = Arr::random(ResourceSite::cases());

    $parameters = [
        FilterParser::param() => [
            ExternalResource::ATTRIBUTE_SITE => $siteFilter->localize(),
        ],
        IncludeParser::param() => Studio::RELATION_RESOURCES,
    ];

    $studio = Studio::factory()
        ->has(ExternalResource::factory()->count(fake()->randomDigitNotNull()), Studio::RELATION_RESOURCES)
        ->createOne();

    $studio->unsetRelations()->load([
        Studio::RELATION_RESOURCES => function (BelongsToMany $query) use ($siteFilter) {
            $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
        },
    ]);

    $response = get(route('api.studio.show', ['studio' => $studio] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioJsonResource($studio, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('images by facet', function () {
    $facetFilter = Arr::random(ImageFacet::cases());

    $parameters = [
        FilterParser::param() => [
            Image::ATTRIBUTE_FACET => $facetFilter->localize(),
        ],
        IncludeParser::param() => Studio::RELATION_IMAGES,
    ];

    $studio = Studio::factory()
        ->has(Image::factory()->count(fake()->randomDigitNotNull()))
        ->createOne();

    $studio->unsetRelations()->load([
        Studio::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter) {
            $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
        },
    ]);

    $response = get(route('api.studio.show', ['studio' => $studio] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioJsonResource($studio, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
