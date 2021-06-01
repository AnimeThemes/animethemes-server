<?php

declare(strict_types=1);

namespace Jetstream;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Http\Livewire\ApiTokenManager;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class CreateApiTokenTest.
 */
class CreateApiTokenTest extends TestCase
{
    use RefreshDatabase;

    public function testApiTokensCanBeCreated()
    {
        if (! Features::hasApiFeatures()) {
            static::markTestSkipped('API support is not enabled.');
        }

        if (Features::hasTeamFeatures()) {
            $this->actingAs($user = User::factory()->withPersonalTeam()->create());
        } else {
            $this->actingAs($user = User::factory()->create());
        }

        Livewire::test(ApiTokenManager::class)
                    ->set(['createApiTokenForm' => [
                        'name' => 'Test Token',
                        'permissions' => [
                            'read',
                            'update',
                        ],
                    ]])
                    ->call('createApiToken');

        static::assertCount(1, $user->fresh()->tokens);
        static::assertEquals('Test Token', $user->fresh()->tokens->first()->name);
        static::assertTrue($user->fresh()->tokens->first()->can('read'));
        static::assertFalse($user->fresh()->tokens->first()->can('delete'));
    }
}
