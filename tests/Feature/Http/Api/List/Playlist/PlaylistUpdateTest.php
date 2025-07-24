<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist;

use App\Constants\Config\ValidationConstants;
use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\Enums\Rules\ModerationService;
use App\Events\List\Playlist\PlaylistCreated;
use App\Features\AllowPlaylistManagement;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PlaylistUpdateTest extends TestCase
{
    use WithFaker;

    /**
     * The Playlist Update Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

        $playlist = Playlist::factory()->createOne();

        $visibility = Arr::random(PlaylistVisibility::cases());

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize()],
        );

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Playlist Update Endpoint shall forbid users without the update playlist permission.
     */
    public function testForbiddenIfMissingPermission(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

        $playlist = Playlist::factory()->createOne();

        $visibility = Arr::random(PlaylistVisibility::cases());

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize()],
        );

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Playlist Update Endpoint shall forbid users from updating the playlist if they don't own it.
     */
    public function testForbiddenIfNotOwnPlaylist(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne();

        $visibility = Arr::random(PlaylistVisibility::cases());

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize()],
        );

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Playlist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Playlist Update Endpoint shall forbid users from updating playlists
     * if the Allow Playlist Management feature is inactive.
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::deactivate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Playlist::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $visibility = Arr::random(PlaylistVisibility::cases());

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [
                Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Playlist Update Endpoint shall update a playlist.
     */
    public function testUpdate(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Playlist::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $visibility = Arr::random(PlaylistVisibility::cases());

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [
                Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertOk();
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to update playlists
     * even if the Allow Playlist Management feature is inactive.
     */
    public function testUpdatePermittedForBypass(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class, $this->faker->boolean());

        $user = User::factory()
            ->withPermissions(
                CrudPermission::UPDATE->format(Playlist::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS->value
            )
            ->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $visibility = Arr::random(PlaylistVisibility::cases());

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [
                Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertOk();
    }

    /**
     * The Playlist Update Endpoint shall update a playlist if the name is not flagged by OpenAI.
     */
    public function testUpdatedIfNotFlaggedByOpenAi(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);
        Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI->value);

        Http::fake([
            'https://api.openai.com/v1/moderations' => Http::response([
                'results' => [
                    0 => [
                        'flagged' => false,
                    ],
                ],
            ]),
        ]);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Playlist::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $visibility = Arr::random(PlaylistVisibility::cases());

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [
                Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertOk();
    }

    /**
     * The Playlist Update Endpoint shall update a playlist if the moderation service returns some error.
     */
    public function testUpdatedIfOpenAiFails(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);
        Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI->value);

        Http::fake([
            'https://api.openai.com/v1/moderations' => Http::response(status: 404),
        ]);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Playlist::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $visibility = Arr::random(PlaylistVisibility::cases());

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [
                Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertOk();
    }

    /**
     * The Playlist Update Endpoint shall prohibit users from updating playlists with names flagged by OpenAI.
     */
    public function testValidationErrorWhenFlaggedByOpenAi(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);
        Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI->value);

        Http::fake([
            'https://api.openai.com/v1/moderations' => Http::response([
                'results' => [
                    0 => [
                        'flagged' => true,
                    ],
                ],
            ]),
        ]);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Playlist::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $visibility = Arr::random(PlaylistVisibility::cases());

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [
                Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertJsonValidationErrors([
            Playlist::ATTRIBUTE_NAME,
        ]);
    }
}
