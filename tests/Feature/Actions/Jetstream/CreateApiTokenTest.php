<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Jetstream;

use App\Models\Auth\User;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Http\Livewire\ApiTokenManager;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class CreateApiTokenTest.
 */
class CreateApiTokenTest extends TestCase
{
    /**
     * API tokens can be created.
     *
     * @return void
     */
    public function testApiTokensCanBeCreated(): void
    {
        if (! Features::hasApiFeatures()) {
            static::markTestSkipped('API support is not enabled.');
        }

        if (Features::hasTeamFeatures()) {
            $this->actingAs($user = User::factory()->withPersonalTeam()->createOne());
        } else {
            $this->actingAs($user = User::factory()->createOne());
        }

        Livewire::test(ApiTokenManager::class)
                    ->set(['createApiTokenForm' => [
                        'name' => 'Test Token',
                        'permissions' => [
                            'anime:read',
                            'anime:update',
                        ],
                    ]])
                    ->call('createApiToken');

        static::assertCount(1, $user->fresh()->tokens);
        static::assertEquals('Test Token', $user->fresh()->tokens->first()->name);
        static::assertTrue($user->fresh()->tokens->first()->can('anime:read'));
        static::assertFalse($user->fresh()->tokens->first()->can('anime:delete'));
    }
}
