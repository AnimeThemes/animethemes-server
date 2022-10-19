<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist;

use App\Enums\Models\List\PlaylistVisibility;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PlaylistUpdateTest.
 */
class PlaylistUpdateTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Playlist Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $playlist = Playlist::factory()->createOne();

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomInstance()->description],
        );

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Playlist Update Endpoint shall forbid users without the update playlist permission.
     *
     * @return void
     */
    public function testForbiddenIfMissingPermission(): void
    {
        $playlist = Playlist::factory()->createOne();

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomInstance()->description],
        );

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Playlist Update Endpoint shall forbid users from updating the playlist if they don't own it.
     *
     * @return void
     */
    public function testForbiddenIfNotOwnPlaylist(): void
    {
        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne();

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomInstance()->description],
        );

        $user = User::factory()->withPermission('update playlist')->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Playlist Update Endpoint shall restrict the first track to a track within the playlist.
     *
     * @return void
     */
    public function testScopeFirst(): void
    {
        $user = User::factory()->withPermission('update playlist')->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $first = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomInstance()->description,
                Playlist::ATTRIBUTE_FIRST => $first->getKey(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertJsonValidationErrors([
            Playlist::ATTRIBUTE_FIRST,
        ]);
    }

    /**
     * The Playlist Update Endpoint shall restrict the last track to a track within the playlist.
     *
     * @return void
     */
    public function testScopeLast(): void
    {
        $user = User::factory()->withPermission('update playlist')->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $last = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomInstance()->description,
                Playlist::ATTRIBUTE_LAST => $last->getKey(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertJsonValidationErrors([
            Playlist::ATTRIBUTE_LAST,
        ]);
    }

    /**
     * The Playlist Update Endpoint shall update a playlist.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $user = User::factory()->withPermission('update playlist')->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $first = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $last = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomInstance()->description,
                Playlist::ATTRIBUTE_FIRST => $first->getKey(),
                Playlist::ATTRIBUTE_LAST => $last->getKey(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertOk();
    }
}
