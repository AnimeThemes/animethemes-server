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
        mutation($playlist: String!, $id: String!, $entryId: Int, $videoId: Int) {
            UpdatePlaylistTrack(playlist: $playlist, id: $id, entryId: $entryId, videoId: $videoId) {
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
            'playlist' => fake()->word(),
            'id' => fake()->word(),
        ],
    ]);

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'authorization');
});

test('forbidden', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    actingAs(User::factory()->createOne());

    $playlist = Playlist::factory()->createOne();

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            // Needed for the bind resolver.
            'playlist' => $playlist->hashid,
            'id' => PlaylistTrack::factory()->for($playlist)->createOne()->hashid,
        ],
    ]);

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'authorization');
});

test('forbidden if not owner', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $user = User::factory()
        ->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))
        ->createOne();

    actingAs($user);

    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->createOne();

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            'playlist' => $track->playlist->hashid,
            'id' => $track->hashid,
        ],
    ]);

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'authorization');
});

it('updates', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $user = User::factory()
        ->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))
        ->createOne();

    actingAs($user);

    $track = PlaylistTrack::factory()
        ->for(Playlist::factory()->for($user))
        ->createOne();

    $entry = AnimeThemeEntry::factory()->createOne();
    $video = Video::factory()->createOne();

    $entry->videos()->attach($video);

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            'playlist' => $track->playlist->hashid,
            'id' => $track->hashid,
            'entryId' => $entry->getKey(),
            'videoId' => $video->getKey(),
        ],
    ]);

    $response->assertOk();
    $response->assertJson([
        'data' => [
            'UpdatePlaylistTrack' => [
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
