<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;

test('fails query season anime field without year', function (): void {
    $animes = Anime::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = $this->graphQL(
        '
        query($season: AnimeSeason!) {
            animeyears {
                year
                season(season: $season) {
                    season
                    anime {
                        data {
                            id
                        }
                    }
                }
                seasons {
                    season
                    anime {
                        data {
                            id
                        }
                    }
                }
            }
        }
        ',
        [
            'season' => $animes->random()->getAttribute(Anime::ATTRIBUTE_SEASON)->name,
        ],
    );

    $response->assertOk();
    $response->assertGraphQLValidationKeys(['year']);
});

test('query season & seasons field', function (): void {
    $animes = Anime::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = $this->graphQL(
        '
        query($season: AnimeSeason!) {
            animeyears {
                year
                season(season: $season) {
                    season
                }
                seasons {
                    season
                }
            }
        }
        ',
        [
            'season' => $animes->random()->getAttribute(Anime::ATTRIBUTE_SEASON)->name,
        ],
    );

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            'animeyears' => [[
                'year',
                'season' => [
                    'season',
                ],
            ]],
        ],
    ]);
});

test('query season anime field with year', function (): void {
    $animes = Anime::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $random = $animes->random();

    $response = $this->graphQL(
        '
        query($season: AnimeSeason!, $year: [Int!]) {
            animeyears(year: $year) {
                year
                season(season: $season) {
                    season
                    anime {
                        data {
                            id
                        }
                    }
                }
                seasons {
                    season
                    anime {
                        data {
                            id
                        }
                    }
                }
            }
        }
        ',
        [
            'season' => $random->getAttribute(Anime::ATTRIBUTE_SEASON)->name,
            'year' => $random->getAttribute(Anime::ATTRIBUTE_YEAR),
        ],
    );

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            'animeyears' => [[
                'year',
                'season' => [
                    'season',
                    'anime' => [
                        'data' => [['id']],
                    ],
                ],
                'seasons' => [[
                    'season',
                    'anime' => [
                        'data' => [['id']],
                    ],
                ]],
            ]],
        ],
    ]);
});
