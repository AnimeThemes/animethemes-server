<?php

declare(strict_types=1);

namespace Jetstream;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Http\Livewire\ApiTokenManager;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class DeleteApiTokenTest
 * @package Jetstream
 */
class DeleteApiTokenTest extends TestCase
{
    use RefreshDatabase;

    public function testApiTokensCanBeDeleted()
    {
        if (! Features::hasApiFeatures()) {
            static::markTestSkipped('API support is not enabled.');
        }

        if (Features::hasTeamFeatures()) {
            $this->actingAs($user = User::factory()->withPersonalTeam()->create());
        } else {
            $this->actingAs($user = User::factory()->create());
        }

        $token = $user->tokens()->create([
            'name' => 'Test Token',
            'token' => Str::random(40),
            'abilities' => ['create', 'read'],
        ]);

        Livewire::test(ApiTokenManager::class)
                    ->set(['apiTokenIdBeingDeleted' => $token->id])
                    ->call('deleteApiToken');

        static::assertCount(0, $user->fresh()->tokens);
    }
}
