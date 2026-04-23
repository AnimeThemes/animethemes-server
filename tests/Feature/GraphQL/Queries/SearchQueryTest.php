<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;

test('searches attributes', function (): void {
    Config::set('scout.driver', 'collection');

    $response = $this->graphQL(
        '
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
        [
            'search' => fake()->word(),
        ],
    );

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
