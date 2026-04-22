<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Events\List\Playlist\PlaylistCreated;
use App\Events\List\Playlist\Track\TrackCreated;
use App\Features\AllowPlaylistManagement;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

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
    $response = $this->graphQL(
        $this->mutation,
        [
            'playlist' => fake()->word(),
            'entryId' => fake()->randomDigitNotNull(),
            'videoId' => fake()->randomDigitNotNull(),
        ],
    );

    $response->assertOk();
    $response->assertJsonPath('errors.0.message', 'This action is unauthorized.');
});

test('forbidden', function () {
    Event::fakeExcept(PlaylistCreated::class);

    actingAs(User::factory()->createOne());

    $response = $this->graphQL(
        $this->mutation,
        [
            // Needed for the bind resolver.
            'playlist' => Playlist::factory()->createOne()->hashid,
            'entryId' => AnimeThemeEntry::factory()->createOne()->getKey(),
            'videoId' => Video::factory()->createOne()->getKey(),
        ],
    );

    $response->assertOk();
    $response->assertJsonPath('errors.0.message', 'This action is unauthorized.');
});

test('forbidden if feature flag is disabled', function () {
    Feature::deactivate(AllowPlaylistManagement::class);

    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $user = User::factory()
        ->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))
        ->createOne();

    actingAs($user);

    $response = $this->graphQL(
        $this->mutation,
        [
            // Needed for the bind resolver.
            'playlist' => Playlist::factory()->createOne()->hashid,
            'entryId' => AnimeThemeEntry::factory()->createOne()->getKey(),
            'videoId' => Video::factory()->createOne()->getKey(),
        ],
    );

    $response->assertOk();
    $response->assertJsonPath('errors.0.message', 'This action is unauthorized.');
});

it('fails if no entry video link', function () {
    Feature::activate(AllowPlaylistManagement::class);

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

    $response = $this->graphQL(
        $this->mutation,
        [
            'playlist' => $playlist->hashid,
            'entryId' => $entry->getKey(),
            'videoId' => $video->getKey(),
        ],
    );

    $response->assertOk();
    $response->assertGraphQLValidationKeys(['entryId', 'videoId']);
});

it('creates', function () {
    Feature::activate(AllowPlaylistManagement::class);

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

    $response = $this->graphQL(
        $this->mutation,
        [
            'playlist' => $playlist->hashid,
            'entryId' => $entry->getKey(),
            'videoId' => $video->getKey(),
        ],
    );

    $this->assertDatabaseCount(PlaylistTrack::class, 1);
    $response->assertOk();
    $response->assertJsonIsObject('data.CreatePlaylistTrack');
});
