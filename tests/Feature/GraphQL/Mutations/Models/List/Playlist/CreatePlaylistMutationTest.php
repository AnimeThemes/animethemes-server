<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\Events\List\Playlist\PlaylistCreated;
use App\Features\AllowPlaylistManagement;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
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

test('protected', function (): void {
    $response = $this->graphQL(
        $this->mutation,
        [
            'name' => fake()->word(),
            'visibility' => Arr::random(PlaylistVisibility::cases())->name,
        ],
    );

    $response->assertOk();
    $response->assertJsonPath('errors.0.message', 'This action is unauthorized.');
});

test('forbidden', function (): void {
    actingAs(User::factory()->createOne());

    $response = $this->graphQL(
        $this->mutation,
        [
            'name' => fake()->word(),
            'visibility' => Arr::random(PlaylistVisibility::cases())->name,
        ],
    );

    $response->assertOk();
    $response->assertJsonPath('errors.0.message', 'This action is unauthorized.');
});

test('forbidden if feature flag is disabled', function (): void {
    Feature::deactivate(AllowPlaylistManagement::class);

    $user = User::factory()
        ->withPermissions(CrudPermission::CREATE->format(Playlist::class))
        ->createOne();

    actingAs($user);

    $response = $this->graphQL(
        $this->mutation,
        [
            'name' => fake()->word(),
            'visibility' => Arr::random(PlaylistVisibility::cases())->name,
        ],
    );

    $response->assertOk();
    $response->assertJsonPath('errors.0.message', 'This action is unauthorized.');
});

it('creates', function (): void {
    Feature::activate(AllowPlaylistManagement::class);

    Event::fakeExcept(PlaylistCreated::class);

    $user = User::factory()
        ->withPermissions(CrudPermission::CREATE->format(Playlist::class))
        ->createOne();

    actingAs($user);

    $playlist = Playlist::factory()->makeOne();

    $response = $this->graphQL(
        $this->mutation,
        [
            'name' => $playlist->name,
            'visibility' => $playlist->visibility->name,
            'description' => $playlist->description,
        ],
    );

    $this->assertDatabaseCount(Playlist::class, 1);
    $response->assertOk();
    $response->assertJsonIsObject('data.CreatePlaylist');
});
