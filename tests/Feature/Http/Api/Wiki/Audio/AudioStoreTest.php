<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Audio;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Audio;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AudioStoreTest extends TestCase
{
    /**
     * The Audio Store Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $audio = Audio::factory()->makeOne();

        $response = $this->post(route('api.audio.store', $audio->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Audio Store Endpoint shall forbid users without the create audio permission.
     */
    public function testForbidden(): void
    {
        $audio = Audio::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.audio.store', $audio->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Audio Store Endpoint shall require basename, filename, mimetype, path & size fields.
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Audio::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.audio.store'));

        $response->assertJsonValidationErrors([
            Audio::ATTRIBUTE_BASENAME,
            Audio::ATTRIBUTE_FILENAME,
            Audio::ATTRIBUTE_MIMETYPE,
            Audio::ATTRIBUTE_PATH,
            Audio::ATTRIBUTE_SIZE,
        ]);
    }

    /**
     * The Audio Store Endpoint shall create an audio.
     */
    public function testCreate(): void
    {
        $parameters = Audio::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Audio::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.audio.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Audio::class, 1);
    }
}
