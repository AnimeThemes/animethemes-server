<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\User\WatchHistory;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->mutation = '
        mutation($entryId: Int!, $videoId: Int!) {
            Watch(entryId: $entryId, videoId: $videoId) {
                animethemeentry {
                    id
                }
                video {
                    id
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
            'videoId' => fake()->randomDigitNotNull(),
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
            'entryId' => fake()->randomDigitNotNull(),
            'videoId' => fake()->randomDigitNotNull(),
        ],
    ]);

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'authorization');
});

test('invalid entry id or video id', function () {
    $user = User::factory()
        ->withPermissions(CrudPermission::CREATE->format(WatchHistory::class))
        ->createOne();

    actingAs($user);

    $entry = AnimeThemeEntry::factory()->createOne();
    $video = Video::factory()->createOne();

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            'entryId' => $entry->getKey(),
            'videoId' => $video->getKey(),
        ],
    ]);

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'validation');
    $this->assertArrayHasKey('entryId', $response->json('errors.0.extensions.validation'));
    $this->assertArrayHasKey('videoId', $response->json('errors.0.extensions.validation'));
});

test('mark as watched', function () {
    $user = User::factory()
        ->withPermissions(CrudPermission::CREATE->format(WatchHistory::class))
        ->createOne();

    actingAs($user);

    $entry = AnimeThemeEntry::factory()->createOne();
    $video = Video::factory()->createOne();

    $entry->videos()->attach($video);

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            'entryId' => $entry->getKey(),
            'videoId' => $video->getKey(),
        ],
    ]);

    $this->assertDatabaseCount(WatchHistory::class, 1);
    $response->assertOk();
    $response->assertJson([
        'data' => [
            'Watch' => [
                'animethemeentry' => [
                    'id' => $entry->getKey(),
                ],
                'video' => [
                    'id' => $video->getKey(),
                ],
            ],
        ],
    ]);
});
