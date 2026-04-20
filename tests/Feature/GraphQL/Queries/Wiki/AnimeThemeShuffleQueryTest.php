<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\AnimeFormat;
use App\Enums\Models\Wiki\ThemeType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Support\Arr;
use Illuminate\Testing\Fluent\AssertableJson;

test('filter by theme type', function () {
    $type = Arr::random(ThemeType::cases());

    AnimeTheme::factory()
        ->state([AnimeTheme::ATTRIBUTE_TYPE => $type->value])
        ->has(AnimeThemeEntry::factory()->has(Video::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    AnimeTheme::factory()
        ->has(AnimeThemeEntry::factory()->has(Video::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = $this->graphQL(
        '
        query($type: [ThemeType!]) {
            animethemeShuffle(type: $type) {
                type
            }
        }
        ',
        [
            'type' => [$type->name],
        ],
    );

    $response->assertOk();
    $response->assertJsonCount(1);
    $response->assertJson(
        fn (AssertableJson $json) => $json->has(
            'data.animethemeShuffle',
            fn (AssertableJson $themes) => $themes->each(fn (AssertableJson $theme) => $theme->where(AnimeTheme::ATTRIBUTE_TYPE, $type->name))
        )
    );
});

test('filter by anime format', function () {
    $format = Arr::random(AnimeFormat::cases());

    AnimeTheme::factory()
        ->for(Anime::factory()->state([Anime::ATTRIBUTE_FORMAT => $format->value]))
        ->has(AnimeThemeEntry::factory()->has(Video::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    AnimeTheme::factory()
        ->has(AnimeThemeEntry::factory()->has(Video::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = $this->graphQL(
        '
        query($format: [AnimeFormat!]) {
            animethemeShuffle(format: $format) {
                anime {
                    format
                }
            }
        }
        ',
        [
            'format' => [$format->name],
        ],
    );

    $response->assertOk();
    $response->assertJsonCount(1);
    $response->assertJson(
        fn (AssertableJson $json) => $json->has(
            'data.animethemeShuffle',
            fn (AssertableJson $themes) => $themes->each(fn (AssertableJson $theme) => $theme->where('anime.format', $format->name))
        )
    );
});

test('filter by year', function () {
    $yearGte = fake()->numberBetween(1964, date('Y') - 1);
    $yearLte = fake()->numberBetween($yearGte, date('Y'));

    AnimeTheme::factory()
        ->for(Anime::factory()->state([Anime::ATTRIBUTE_YEAR => fake()->numberBetween($yearLte, $yearGte)]))
        ->has(AnimeThemeEntry::factory()->has(Video::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    AnimeTheme::factory()
        ->has(AnimeThemeEntry::factory()->has(Video::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = $this->graphQL(
        '
        query($yearLte: Int, $yearGte: Int) {
            animethemeShuffle(year_lte: $yearLte, year_gte: $yearGte) {
                anime {
                    year
                }
            }
        }
        ',
        [
            'yearLte' => $yearLte,
            'yearGte' => $yearGte,
        ],
    );

    $response->assertOk();
    $response->assertJsonCount(1);
    $response->assertJson(
        fn (AssertableJson $json) => $json->has(
            'data.animethemeShuffle',
            fn (AssertableJson $themes) => $themes->each(fn (AssertableJson $theme) => $theme->where('anime.year', fn (int $year) => $year <= $yearLte)
                ->where('anime.year', fn (int $year) => $year >= $yearGte))
        )
    );
});

test('filter by entry spoiler', function () {
    $spoiler = fake()->boolean();

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(AnimeThemeEntry::factory()->state([AnimeThemeEntry::ATTRIBUTE_SPOILER => $spoiler])->has(Video::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    AnimeTheme::factory()
        ->has(AnimeThemeEntry::factory()->has(Video::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = $this->graphQL(
        '
        query($spoiler: Boolean) {
            animethemeShuffle(spoiler: $spoiler) {
                animethemeentries {
                    spoiler
                }
            }
        }
        ',
        [
            'spoiler' => $spoiler,
        ],
    );

    $response->assertOk();
    $response->assertJsonCount(1);
    $response->assertJson(
        fn (AssertableJson $json) => $json->has(
            'data.animethemeShuffle',
            fn (AssertableJson $themes) => $themes->each(
                fn (AssertableJson $theme) => $theme->has(
                    AnimeTheme::RELATION_ENTRIES,
                    fn (AssertableJson $entries) => $entries->each(
                        fn (AssertableJson $entry) => $entry->where(AnimeThemeEntry::ATTRIBUTE_SPOILER, $spoiler)
                    )
                )
            )
        )
    );
});
