<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Jetstream;

use App\Models\Auth\User;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Http\Livewire\ApiTokenManager;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class DeleteApiTokenTest.
 */
class DeleteApiTokenTest extends TestCase
{
    /**
     * API tokens can be deleted.
     *
     * @return void
     */
    public function testApiTokensCanBeDeleted()
    {
        if (! Features::hasApiFeatures()) {
            static::markTestSkipped('API support is not enabled.');
        }

        if (Features::hasTeamFeatures()) {
            $this->actingAs($user = User::factory()->withPersonalTeam()->createOne());
        } else {
            $this->actingAs($user = User::factory()->createOne());
        }

        $token = $user->tokens()->create([
            'name' => 'Test Token',
            'token' => Str::random(40),
            'abilities' => ['create', 'read'],
        ]);

        Livewire::test(ApiTokenManager::class)
                    ->set(['apiTokenIdBeingDeleted' => $token->getKey()])
                    ->call('deleteApiToken');

        static::assertCount(0, $user->fresh()->tokens);
    }
}
