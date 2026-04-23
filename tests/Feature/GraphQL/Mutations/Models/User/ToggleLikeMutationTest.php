<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\User\Like;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
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

test('protected', function (): void {
    $response = $this->graphQL(
        $this->mutation,
        [
            'entryId' => fake()->randomDigitNotNull(),
        ],
    );

    $response->assertOk();
    $response->assertJsonPath('errors.0.message', 'This action is unauthorized.');
});

test('forbidden', function (): void {
    actingAs(User::factory()->createOne());

    $response = $this->graphQL(
        $this->mutation,
        [
            // Needed for the bind resolver.
            'entryId' => AnimeThemeEntry::factory()->createOne()->getKey(),
        ],
    );

    $response->assertOk();
    $response->assertJsonPath('errors.0.message', 'This action is unauthorized.');
});

it('fails if more than one resource is passed', function (): void {
    $user = User::factory()
        ->withPermissions(CrudPermission::CREATE->format(Like::class))
        ->createOne();

    actingAs($user);

    $entry = AnimeThemeEntry::factory()->createOne();
    $playlist = Playlist::factory()->createOne();

    $response = $this->graphQL(
        $this->mutation,
        [
            'entryId' => $entry->getKey(),
            'playlistId' => $playlist->hashid,
        ],
    );

    $response->assertOk();
    $response->assertGraphQLValidationKeys(['entry', 'playlist']);
});

it('likes entry', function (): void {
    $user = User::factory()
        ->withPermissions(CrudPermission::CREATE->format(Like::class))
        ->createOne();

    actingAs($user);

    $entry = AnimeThemeEntry::factory()->createOne();

    $response = $this->graphQL(
        $this->mutation,
        [
            'entryId' => $entry->getKey(),
        ],
    );

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

it('likes playlist', function (): void {
    $user = User::factory()
        ->withPermissions(CrudPermission::CREATE->format(Like::class))
        ->createOne();

    actingAs($user);

    $playlist = Playlist::factory()->createOne([
        Playlist::ATTRIBUTE_HASHID => fake()->word(),
    ]);

    $response = $this->graphQL(
        $this->mutation,
        [
            'playlistId' => $playlist->hashid,
        ],
    );

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
