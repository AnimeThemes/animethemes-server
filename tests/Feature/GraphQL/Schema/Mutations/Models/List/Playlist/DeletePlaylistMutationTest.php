<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\List\Playlist;

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
    actingAs(User::factory()->createOne());

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            // Needed for the bind resolver.
            'id' => Playlist::factory([Playlist::ATTRIBUTE_HASHID => fake()->word()])->createOne()->hashid,
        ],
    ]);

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'authorization');
});

test('forbidden if not owner', function () {
    $user = User::factory()
        ->withPermissions(CrudPermission::DELETE->format(Playlist::class))
        ->createOne();

    actingAs($user);

    $playlist = Playlist::factory()
        ->createOne([
            Playlist::ATTRIBUTE_HASHID => fake()->word(),
        ]);

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            'id' => $playlist->hashid,
        ],
    ]);

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'authorization');
});

it('deletes', function () {
    $user = User::factory()
        ->withPermissions(CrudPermission::DELETE->format(Playlist::class))
        ->createOne();

    actingAs($user);

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne([
            Playlist::ATTRIBUTE_HASHID => fake()->word(),
        ]);

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            'id' => $playlist->hashid,
        ],
    ]);

    $response->assertOk();
    $this->assertIsString($response->json('data.DeletePlaylist.message'));
});
