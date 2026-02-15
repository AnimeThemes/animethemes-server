<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\Events\List\Playlist\PlaylistCreated;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->mutation = '
        mutation($name: String!, $visibility: PlaylistVisibility!, $description: String) {
            CreatePlaylist(name: $name, visibility: $visibility, description: $description) {
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
            'name' => fake()->word(),
            'visibility' => Arr::random(PlaylistVisibility::cases())->name,
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
            'name' => fake()->word(),
            'visibility' => Arr::random(PlaylistVisibility::cases())->name,
        ],
    ]);

    $response->assertOk();
    $response->assertJsonPath('errors.0.extensions.category', 'authorization');
});

it('creates', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $user = User::factory()
        ->withPermissions(CrudPermission::CREATE->format(Playlist::class))
        ->createOne();

    actingAs($user);

    $playlist = Playlist::factory()->makeOne();

    $response = graphql([
        'query' => $this->mutation,
        'variables' => [
            'name' => $playlist->name,
            'visibility' => $playlist->visibility->name,
            'description' => $playlist->description,
        ],
    ]);

    $this->assertDatabaseCount(Playlist::class, 1);
    $response->assertOk();
    $response->assertJsonIsObject('data.CreatePlaylist');
});
