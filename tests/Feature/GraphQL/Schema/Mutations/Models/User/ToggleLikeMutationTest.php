<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\User\Like;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

use function Pest\Laravel\actingAs;

pest()->only();

beforeEach(function () {
    $this->mutation = '
        mutation($entryId: Int, $playlistId: String) {
            ToggleLike(entry: $entryId, playlist: $playlistId) {
                animethemeentry: likeable {
                    ... on AnimeThemeEntry {
                        id
                    }
                }
                playlist: likeable {
                    ... on Playlist {
                        id
                    }
                }
            }
        }
    ';
});

test('protected', function () {
    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            'entryId' => fake()->randomDigitNotNull(),
        ],
    ]);

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'authorization');
});

test('forbidden', function () {
    actingAs(User::factory()->createOne());

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            // Needed for the bind resolver.
            'entryId' => AnimeThemeEntry::factory()->createOne()->getKey(),
        ],
    ]);

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'authorization');
});

it('fails if more than one resource is passed', function () {
    $user = User::factory()
        ->withPermissions(CrudPermission::CREATE->format(Like::class))
        ->createOne();

    actingAs($user);

    $entry = AnimeThemeEntry::factory()->createOne();
    $playlist = Playlist::factory()->createOne();

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            'entryId' => $entry->getKey(),
            'playlistId' => $playlist->hashid,
        ],
    ]);

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'validation');
    $this->assertArrayHasKey('entry', $response->json('errors.0.extensions.validation'));
    $this->assertArrayHasKey('playlist', $response->json('errors.0.extensions.validation'));
});

it('likes entry', function () {
    $user = User::factory()
        ->withPermissions(CrudPermission::CREATE->format(Like::class))
        ->createOne();

    actingAs($user);

    $entry = AnimeThemeEntry::factory()->createOne();

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            'entryId' => $entry->getKey(),
        ],
    ]);

    $this->assertDatabaseCount(Like::class, 1);
    $response->assertOk();
    $response->assertJson([
        'data' => [
            'ToggleLike' => [
                'animethemeentry' => [
                    'id' => $entry->getKey(),
                ],
            ],
        ],
    ]);
});

it('likes playlist', function () {
    $user = User::factory()
        ->withPermissions(CrudPermission::CREATE->format(Like::class))
        ->createOne();

    actingAs($user);

    $playlist = Playlist::factory()->createOne([
        Playlist::ATTRIBUTE_HASHID => fake()->word(),
    ]);

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            'playlistId' => $playlist->hashid,
        ],
    ]);

    $this->assertDatabaseCount(Like::class, 1);
    $response->assertOk();
    $response->assertJson([
        'data' => [
            'ToggleLike' => [
                'playlist' => [
                    'id' => $playlist->hashid,
                ],
            ],
        ],
    ]);
});
