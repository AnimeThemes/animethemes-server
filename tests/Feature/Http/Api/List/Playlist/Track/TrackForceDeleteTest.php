<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist\Track;

use App\Enums\Models\List\PlaylistVisibility;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class TrackForceDeleteTest.
 */
class TrackForceDeleteTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Track Force Delete Endpoint shall require authorization.
     *
     * @return void
     */
    public function testAuthorized(): void
    {
        $track = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $response = $this->delete(route('api.playlist.track.forceDelete', ['playlist' => $track->playlist, 'track' => $track]));

        $response->assertUnauthorized();
    }

    /**
     * The Track Force Delete Endpoint shall forbid users without the force delete playlist track permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $track = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.forceDelete', ['playlist' => $track->playlist, 'track' => $track]));

        $response->assertForbidden();
    }

    /**
     * The Track Force Delete Endpoint shall scope bindings.
     *
     * @return void
     */
    public function testScoped(): void
    {
        $user = User::factory()->withPermission('force delete playlist track')->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->has(PlaylistTrack::factory()->count($this->faker->randomDigitNotNull()), Playlist::RELATION_TRACKS)
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory()->for(User::factory()))
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.forceDelete', ['playlist' => $playlist, 'track' => $track]));

        $response->assertNotFound();
    }

    /**
     * The Track Force Delete Endpoint shall force delete the playlist track.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $track = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $user = User::factory()->withPermission('force delete playlist track')->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.forceDelete', ['playlist' => $track->playlist, 'track' => $track]));

        $response->assertOk();
        static::assertModelMissing($track);
    }
}
