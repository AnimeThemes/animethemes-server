<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Events\List\Playlist\PlaylistCreated;
use App\Events\List\Playlist\Track\TrackCreated;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->mutation = '
        mutation($playlist: String!, $entryId: Int!, $videoId: Int!) {
            CreatePlaylistTrack(playlist: $playlist, entryId: $entryId, videoId: $videoId) {
                id
            }
        }
    ';
});

test('protected', function () {
    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            'playlist' => fake()->word(),
            'entryId' => fake()->randomDigitNotNull(),
            'videoId' => fake()->randomDigitNotNull(),
        ],
    ]);

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'authorization');
});

test('forbidden', function () {
    Event::fakeExcept(PlaylistCreated::class);

    actingAs(User::factory()->createOne());

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            // Needed for the bind resolver.
            'playlist' => Playlist::factory()->createOne()->hashid,
            'entryId' => AnimeThemeEntry::factory()->createOne()->getKey(),
            'videoId' => Video::factory()->createOne()->getKey(),
        ],
    ]);

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'authorization');
});

it('fails if no entry video link', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $user = User::factory()
        ->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))
        ->createOne();

    actingAs($user);

    $entry = AnimeThemeEntry::factory()->createOne();
    $video = Video::factory()->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            'playlist' => $playlist->hashid,
            'entryId' => $entry->getKey(),
            'videoId' => $video->getKey(),
        ],
    ]);

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'validation');
    $this->assertArrayHasKey('entryId', $response->json('errors.0.extensions.validation'));
    $this->assertArrayHasKey('videoId', $response->json('errors.0.extensions.validation'));
});

it('creates', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $user = User::factory()
        ->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))
        ->createOne();

    actingAs($user);

    $entry = AnimeThemeEntry::factory()->createOne();
    $video = Video::factory()->createOne();

    $entry->videos()->attach($video);

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            'playlist' => $playlist->hashid,
            'entryId' => $entry->getKey(),
            'videoId' => $video->getKey(),
        ],
    ]);

    $this->assertDatabaseCount(PlaylistTrack::class, 1);
    $response->assertOk();
    $response->assertJsonIsObject('data.CreatePlaylistTrack');
});
