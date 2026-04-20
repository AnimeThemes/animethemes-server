<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Events\List\Playlist\PlaylistCreated;
use App\Features\AllowPlaylistManagement;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

use function Pest\Laravel\actingAs;

pest()->only();

beforeEach(function () {
    $this->mutation = '
        mutation($id: String!) {
            DeletePlaylist(id: $id) {
                message
            }
        }
    ';
});

test('protected', function () {
    $response = $this->graphQL(
        $this->mutation,
        [
            'id' => fake()->word(),
        ],
    );

    $response->ddJson();
    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'authorization');
})->only();

test('forbidden', function () {
    Event::fakeExcept(PlaylistCreated::class);

    actingAs(User::factory()->createOne());

    $response = $this->graphQL(
        $this->mutation,
        [
            // Needed for the bind resolver.
            'id' => Playlist::factory()->createOne()->hashid,
        ],
    );

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'authorization');
});

test('forbidden if feature flag is disabled', function () {
    Feature::deactivate(AllowPlaylistManagement::class);

    Event::fakeExcept(PlaylistCreated::class);

    $user = User::factory()
        ->withPermissions(CrudPermission::DELETE->format(Playlist::class))
        ->createOne();

    actingAs($user);

    $response = $this->graphQL(
        $this->mutation,
        [
            // Needed for the bind resolver.
            'id' => Playlist::factory()->createOne()->hashid,
        ],
    );

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'authorization');
});

test('forbidden if not owner', function () {
    Feature::activate(AllowPlaylistManagement::class);

    Event::fakeExcept(PlaylistCreated::class);

    $user = User::factory()
        ->withPermissions(CrudPermission::DELETE->format(Playlist::class))
        ->createOne();

    actingAs($user);

    $response = $this->graphQL(
        $this->mutation,
        [
            'id' => Playlist::factory()->createOne()->hashid,
        ],
    );

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'authorization');
});

it('deletes', function () {
    Feature::activate(AllowPlaylistManagement::class);

    Event::fakeExcept(PlaylistCreated::class);

    $user = User::factory()
        ->withPermissions(CrudPermission::DELETE->format(Playlist::class))
        ->createOne();

    actingAs($user);

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();

    $response = $this->graphQL(
        $this->mutation,
        [
            'id' => $playlist->hashid,
        ],
    );

    $this->assertDatabaseCount(Playlist::class, 0);
    $response->assertOk();
    $this->assertIsString($response->json('data.DeletePlaylist.message'));
});
