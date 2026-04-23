<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Events\List\Playlist\PlaylistCreated;
use App\Events\List\Playlist\Track\TrackCreated;
use App\Features\AllowPlaylistManagement;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    $this->mutation = '
        mutation($playlist: String!, $id: String!) {
            DeletePlaylistTrack(playlist: $playlist, id: $id) {
                message
            }
        }
    ';
});

test('protected', function (): void {
    $response = $this->graphQL(
        $this->mutation,
        [
            'playlist' => fake()->word(),
            'id' => fake()->word(),
        ],
    );

    $response->assertOk();
    $response->assertJsonPath('errors.0.message', 'This action is unauthorized.');
});

test('forbidden', function (): void {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    actingAs(User::factory()->createOne());

    $playlist = Playlist::factory()->createOne();

    $response = $this->graphQL(
        $this->mutation,
        [
            // Needed for the bind resolver.
            'playlist' => $playlist->hashid,
            'id' => PlaylistTrack::factory()->for($playlist)->createOne()->hashid,
        ],
    );

    $response->assertOk();
    $response->assertJsonPath('errors.0.message', 'This action is unauthorized.');
});

test('forbidden if feature flag is disabled', function (): void {
    Feature::deactivate(AllowPlaylistManagement::class);

    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $user = User::factory()
        ->withPermissions(CrudPermission::DELETE->format(PlaylistTrack::class))
        ->createOne();

    actingAs($user);

    $track = PlaylistTrack::factory()
        ->for(Playlist::factory()->for($user))
        ->createOne();

    $response = $this->graphQL(
        $this->mutation,
        [
            'playlist' => $track->playlist->hashid,
            'id' => $track->hashid,
        ],
    );

    $response->assertOk();
    $response->assertJsonPath('errors.0.message', 'This action is unauthorized.');
});

test('forbidden if not owner', function (): void {
    Feature::activate(AllowPlaylistManagement::class);

    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $user = User::factory()
        ->withPermissions(CrudPermission::DELETE->format(PlaylistTrack::class))
        ->createOne();

    actingAs($user);

    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->createOne();

    $response = $this->graphQL(
        $this->mutation,
        [
            'playlist' => $track->playlist->hashid,
            'id' => $track->hashid,
        ],
    );

    $response->assertOk();
    $response->assertJsonPath('errors.0.message', 'This action is unauthorized.');
});

it('deletes', function (): void {
    Feature::activate(AllowPlaylistManagement::class);

    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $user = User::factory()
        ->withPermissions(CrudPermission::DELETE->format(PlaylistTrack::class))
        ->createOne();

    actingAs($user);

    $track = PlaylistTrack::factory()
        ->for(Playlist::factory()->for($user))
        ->createOne();

    $response = $this->graphQL(
        $this->mutation,
        [
            'playlist' => $track->playlist->hashid,
            'id' => $track->hashid,
        ],
    );

    $this->assertDatabaseCount(PlaylistTrack::class, 0);
    $response->assertOk();
    $this->assertIsString($response->json('data.DeletePlaylistTrack.message'));
});
