<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;

test('fails query season anime field without year', function () {
    $animes = Anime::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = graphql([
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
            'season' => $animes->random()->getAttribute(Anime::ATTRIBUTE_SEASON)->name,
        ],
    ]);

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'validation');
    $this->assertArrayHasKey('year', $response->json('errors.0.extensions.validation'));
});

test('query season & seasons field', function () {
    $animes = Anime::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = graphql([
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

test('query season anime field with year', function () {
    $animes = Anime::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $random = $animes->random();

    $response = graphql([
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
