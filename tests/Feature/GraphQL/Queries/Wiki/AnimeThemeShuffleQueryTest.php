<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\AnimeFormat;
use App\Enums\Models\Wiki\ThemeType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use Illuminate\Support\Arr;
use Illuminate\Testing\Fluent\AssertableJson;

test('filter by theme type', function (): void {
    $type = Arr::random(ThemeType::cases());

    Theme::factory()
        ->state([Theme::ATTRIBUTE_TYPE => $type->value])
        ->has(Entry::factory()->has(Video::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    Theme::factory()
        ->has(Entry::factory()->has(Video::factory()))
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
        fn (AssertableJson $json): AssertableJson => $json->has(
            'data.animethemeShuffle',
            fn (AssertableJson $themes): AssertableJson => $themes->each(fn (AssertableJson $theme): AssertableJson => $theme->where(Theme::ATTRIBUTE_TYPE, $type->name))
        )
    );
});

test('filter by anime format', function (): void {
    $format = Arr::random(AnimeFormat::cases());

    Theme::factory()
        ->for(Anime::factory()->state([Anime::ATTRIBUTE_FORMAT => $format->value]))
        ->has(Entry::factory()->has(Video::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    Theme::factory()
        ->has(Entry::factory()->has(Video::factory()))
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
        fn (AssertableJson $json): AssertableJson => $json->has(
            'data.animethemeShuffle',
            fn (AssertableJson $themes): AssertableJson => $themes->each(fn (AssertableJson $theme): AssertableJson => $theme->where('anime.format', $format->name))
        )
    );
});

test('filter by year', function (): void {
    $yearGte = fake()->numberBetween(1964, date('Y') - 1);
    $yearLte = fake()->numberBetween($yearGte, date('Y'));

    Theme::factory()
        ->for(Anime::factory()->state([Anime::ATTRIBUTE_YEAR => fake()->numberBetween($yearLte, $yearGte)]))
        ->has(Entry::factory()->has(Video::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    Theme::factory()
        ->has(Entry::factory()->has(Video::factory()))
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
        fn (AssertableJson $json): AssertableJson => $json->has(
            'data.animethemeShuffle',
            fn (AssertableJson $themes): AssertableJson => $themes->each(fn (AssertableJson $theme): AssertableJson => $theme->where('anime.year', fn (int $year): bool => $year <= $yearLte)
                ->where('anime.year', fn (int $year): bool => $year >= $yearGte))
        )
    );
});

test('filter by entry spoiler', function (): void {
    $spoiler = fake()->boolean();

    Theme::factory()
        ->for(Anime::factory())
        ->has(Entry::factory()->state([Entry::ATTRIBUTE_SPOILER => $spoiler])->has(Video::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    Theme::factory()
        ->has(Entry::factory()->has(Video::factory()))
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
        fn (AssertableJson $json): AssertableJson => $json->has(
            'data.animethemeShuffle',
            fn (AssertableJson $themes): AssertableJson => $themes->each(
                fn (AssertableJson $theme): AssertableJson => $theme->has(
                    Theme::RELATION_ENTRIES,
                    fn (AssertableJson $entries): AssertableJson => $entries->each(
                        fn (AssertableJson $entry): AssertableJson => $entry->where(Entry::ATTRIBUTE_SPOILER, $spoiler)
                    )
                )
            )
        )
    );
});
