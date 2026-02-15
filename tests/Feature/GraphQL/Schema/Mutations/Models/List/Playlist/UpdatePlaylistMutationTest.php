<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\List\Playlist;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->mutation = '
        mutation($id: String!, $name: String, $visibility: PlaylistVisibility, $description: String) {
            UpdatePlaylist(id: $id, name: $name, visibility: $visibility, description: $description) {
                name
                visibility
                description
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
        ->withPermissions(CrudPermission::UPDATE->format(Playlist::class))
        ->createOne();

    actingAs($user);

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            'id' => Playlist::factory()->createOne([Playlist::ATTRIBUTE_HASHID => fake()->word()])->hashid,
        ],
    ]);

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'authorization');
});

it('updates', function () {
    $user = User::factory()
        ->withPermissions(CrudPermission::UPDATE->format(Playlist::class))
        ->createOne();

    actingAs($user);

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne([
            Playlist::ATTRIBUTE_HASHID => fake()->word(),
        ]);

    $newPlaylist = Playlist::factory()->makeOne();

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            'id' => $playlist->hashid,
            'name' => $newPlaylist->name,
            'visibility' => $newPlaylist->visibility->name,
            'description' => $newPlaylist->description,
        ],
    ]);

    $response->assertOk();
    $response->assertJson([
        'data' => [
            'UpdatePlaylist' => [
                'name' => $newPlaylist->name,
                'visibility' => $newPlaylist->visibility->name,
                'description' => $newPlaylist->description,
            ],
        ],
    ]);
});
