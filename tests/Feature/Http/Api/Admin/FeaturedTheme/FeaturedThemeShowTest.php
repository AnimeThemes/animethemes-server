<?php

declare(strict_types=1);

use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Admin\FeaturedThemeSchema;
use App\Http\Resources\Admin\Resource\FeaturedThemeResource;
use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Models\Wiki\Video;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('forbidden if future start date', function () {
    $featuredTheme = FeaturedTheme::factory()->create([
        FeaturedTheme::ATTRIBUTE_START_AT => fake()->dateTimeBetween('+1 day', '+1 year'),
    ]);

    $response = get(route('api.featuredtheme.show', ['featuredtheme' => $featuredTheme]));

    $response->assertForbidden();
});

test('default', function () {
    $featuredTheme = FeaturedTheme::factory()->create();

    $response = get(route('api.featuredtheme.show', ['featuredtheme' => $featuredTheme]));

    $response->assertJson(
        json_decode(
            json_encode(
                new FeaturedThemeResource($featuredTheme, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new FeaturedThemeSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $featuredTheme = FeaturedTheme::factory()
        ->for(
            AnimeThemeEntry::factory()
                ->for(
                    AnimeTheme::factory()
                        ->for(Anime::factory()->has(Image::factory()->count(fake()->randomDigitNotNull())))
                        ->for(Song::factory()->has(Artist::factory()->count(fake()->randomDigitNotNull())))
                )
        )
        ->for(Video::factory())
        ->for(User::factory())
        ->createOne();

    $response = get(route('api.featuredtheme.show', ['featuredtheme' => $featuredTheme] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new FeaturedThemeResource($featuredTheme, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new FeaturedThemeSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            FeaturedThemeResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $featuredTheme = FeaturedTheme::factory()->create();

    $response = get(route('api.featuredtheme.show', ['featuredtheme' => $featuredTheme] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new FeaturedThemeResource($featuredTheme, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
