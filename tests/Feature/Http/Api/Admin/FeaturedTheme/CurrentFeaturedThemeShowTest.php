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
use Illuminate\Support\Collection;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('not found if no featured themes', function () {
    $response = get(route('api.featuredtheme.current.show'));

    $response->assertNotFound();
});

test('not found if theme start at null', function () {
    FeaturedTheme::factory()->create([
        FeaturedTheme::ATTRIBUTE_START_AT => null,
    ]);

    $response = get(route('api.featuredtheme.current.show'));

    $response->assertNotFound();
});

test('not found if theme end at null', function () {
    FeaturedTheme::factory()->create([
        FeaturedTheme::ATTRIBUTE_END_AT => null,
    ]);

    $response = get(route('api.featuredtheme.current.show'));

    $response->assertNotFound();
});

test('not found if theme start at after now', function () {
    FeaturedTheme::factory()->create([
        FeaturedTheme::ATTRIBUTE_START_AT => fake()->dateTimeBetween('+1 day', '+1 year'),
    ]);

    $response = get(route('api.featuredtheme.current.show'));

    $response->assertNotFound();
});

test('not found if theme end at before now', function () {
    FeaturedTheme::factory()->create([
        FeaturedTheme::ATTRIBUTE_END_AT => fake()->dateTimeBetween(),
    ]);

    $response = get(route('api.featuredtheme.current.show'));

    $response->assertNotFound();
});

test('default', function () {
    Collection::times(fake()->randomDigitNotNull(), function () {
        FeaturedTheme::factory()->create([
            FeaturedTheme::ATTRIBUTE_START_AT => null,
        ]);
    });

    Collection::times(fake()->randomDigitNotNull(), function () {
        FeaturedTheme::factory()->create([
            FeaturedTheme::ATTRIBUTE_END_AT => null,
        ]);
    });

    Collection::times(fake()->randomDigitNotNull(), function () {
        FeaturedTheme::factory()->create([
            FeaturedTheme::ATTRIBUTE_START_AT => fake()->dateTimeBetween('+1 day', '+1 year'),
        ]);
    });

    Collection::times(fake()->randomDigitNotNull(), function () {
        FeaturedTheme::factory()->create([
            FeaturedTheme::ATTRIBUTE_END_AT => fake()->dateTimeBetween('-1 year', '-1 day'),
        ]);
    });

    $currentTheme = FeaturedTheme::factory()->create();

    $response = get(route('api.featuredtheme.current.show'));

    $response->assertJson(
        json_decode(
            json_encode(
                new FeaturedThemeResource($currentTheme, new Query())
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

    $currentTheme = FeaturedTheme::factory()
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

    $response = get(route('api.featuredtheme.current.show', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new FeaturedThemeResource($currentTheme, new Query($parameters))
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

    $currentTheme = FeaturedTheme::factory()->create();

    $response = get(route('api.featuredtheme.current.show', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new FeaturedThemeResource($currentTheme, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
