<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;

use function Pest\Laravel\post;

test('query season & seasons field', function () {
    $animes = Anime::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = post(route('graphql'), [
        'query' => '
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
        'variables' => [
            'season' => $animes->random()->getAttribute(Anime::ATTRIBUTE_SEASON)->name,
        ],
    ]);

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

test('fails query season anime field without year', function () {
    $animes = Anime::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = post(route('graphql'), [
        'query' => '
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
                        season {
                            anime {
                                data {
                                    id
                                }
                            }
                        }
                    }
                }
            }
        ',
        'variables' => [
            'season' => $animes->random()->getAttribute(Anime::ATTRIBUTE_SEASON)->name,
        ],
    ]);

    $response->assertOk();
    $response->assertJsonCount(1, 'errors');
    $response->assertJsonStructure([
        'errors' => [['message']],
    ]);
});

test('query season anime field with year', function () {
    $animes = Anime::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $random = $animes->random();

    $response = post(route('graphql'), [
        'query' => '
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
        'variables' => [
            'season' => $random->getAttribute(Anime::ATTRIBUTE_SEASON)->name,
            'year' => $random->getAttribute(Anime::ATTRIBUTE_YEAR),
        ],
    ]);

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
