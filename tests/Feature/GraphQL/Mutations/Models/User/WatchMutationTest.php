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
    $response = $this->graphQL(
        $this->mutation,
        [
            'entryId' => fake()->randomDigitNotNull(),
            'videoId' => fake()->randomDigitNotNull(),
        ],
    );

    $response->assertOk();
    $response->assertJsonPath('errors.0.message', 'This action is unauthorized.');
});

test('forbidden', function () {
    actingAs(User::factory()->createOne());

    $response = $this->graphQL(
        $this->mutation,
        [
            'entryId' => fake()->randomDigitNotNull(),
            'videoId' => fake()->randomDigitNotNull(),
        ],
    );

    $response->assertOk();
    $response->assertJsonPath('errors.0.message', 'This action is unauthorized.');
});

test('invalid entry id or video id', function () {
    $user = User::factory()
        ->withPermissions(CrudPermission::CREATE->format(WatchHistory::class))
        ->createOne();

    actingAs($user);

    $entry = AnimeThemeEntry::factory()->createOne();
    $video = Video::factory()->createOne();

    $response = $this->graphQL(
        $this->mutation,
        [
            'entryId' => $entry->getKey(),
            'videoId' => $video->getKey(),
        ],
    );

    $response->assertOk();
    $response->assertGraphQLValidationKeys(['entryId', 'videoId']);
});

test('mark as watched', function () {
    $user = User::factory()
        ->withPermissions(CrudPermission::CREATE->format(WatchHistory::class))
        ->createOne();

    actingAs($user);

    $entry = AnimeThemeEntry::factory()->createOne();
    $video = Video::factory()->createOne();

    $entry->videos()->attach($video);

    $response = $this->graphQL(
        $this->mutation,
        [
            'entryId' => $entry->getKey(),
            'videoId' => $video->getKey(),
        ],
    );

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
