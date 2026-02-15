<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Events\List\Playlist\PlaylistCreated;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\actingAs;

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
    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            'id' => fake()->word(),
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
            'id' => Playlist::factory()->createOne()->hashid,
        ],
    ]);

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'authorization');
});

test('forbidden if not owner', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $user = User::factory()
        ->withPermissions(CrudPermission::DELETE->format(Playlist::class))
        ->createOne();

    actingAs($user);

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            'id' => Playlist::factory()->createOne()->hashid,
        ],
    ]);

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'authorization');
});

it('deletes', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $user = User::factory()
        ->withPermissions(CrudPermission::DELETE->format(Playlist::class))
        ->createOne();

    actingAs($user);

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            'id' => $playlist->hashid,
        ],
    ]);

    $this->assertDatabaseCount(Playlist::class, 0);
    $response->assertOk();
    $this->assertIsString($response->json('data.DeletePlaylist.message'));
});
