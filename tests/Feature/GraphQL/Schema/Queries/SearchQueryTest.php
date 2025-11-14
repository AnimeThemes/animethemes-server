<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;

use function Pest\Laravel\post;

test('searches attributes', function () {
    Config::set('scout.driver', 'collection');

    $response = post(route('graphql'), [
        'query' => '
            query($search: String!) {
                search(search: $search) {
                    anime {
                        id
                    }
                    artists {
                        id
                    }
                    animethemes {
                        id
                    }
                    playlists {
                        id
                    }
                    series {
                        id
                    }
                    songs {
                        id
                    }
                    studios {
                        id
                    }
                    videos {
                        id
                    }
                }
            }
        ',
        'variables' => [
            'search' => fake()->word(),
        ],
    ]);

    $response->assertJsonStructure([
        'data' => [
            'search' => [
                'anime',
                'artists',
                'animethemes',
                'playlists',
                'series',
                'songs',
                'studios',
                'videos',
            ],
        ],
    ]);
});
