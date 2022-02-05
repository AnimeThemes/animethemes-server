<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Jetstream;

use App\Models\Auth\User;
use Laravel\Jetstream\Http\Livewire\TwoFactorAuthenticationForm;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class TwoFactorAuthenticationSettingsTest.
 */
class TwoFactorAuthenticationSettingsTest extends TestCase
{
    /**
     * Two-factor authentication can be enabled.
     *
     * @return void
     */
    public function testTwoFactorAuthenticationCanBeEnabled(): void
    {
        $this->actingAs($user = User::factory()->createOne());

        $this->withSession(['auth.password_confirmed_at' => time()]);

        Livewire::test(TwoFactorAuthenticationForm::class)
                ->call('enableTwoFactorAuthentication');

        $user = $user->fresh();

        static::assertNotNull($user->two_factor_secret);
        static::assertCount(8, $user->recoveryCodes());
    }

    /**
     * Recovery codes can be regenerated.
     *
     * @return void
     */
    public function testRecoveryCodesCanBeRegenerated(): void
    {
        $this->actingAs($user = User::factory()->createOne());

        $this->withSession(['auth.password_confirmed_at' => time()]);

        $component = Livewire::test(TwoFactorAuthenticationForm::class)
                ->call('enableTwoFactorAuthentication')
                ->call('regenerateRecoveryCodes');

        $user = $user->fresh();

        $component->call('regenerateRecoveryCodes');

        static::assertCount(8, $user->recoveryCodes());
        static::assertCount(8, array_diff($user->recoveryCodes(), $user->fresh()->recoveryCodes()));
    }

    /**
     * Two-factor authentication can be disabled.
     *
     * @return void
     */
    public function testTwoFactorAuthenticationCanBeDisabled(): void
    {
        $this->actingAs($user = User::factory()->createOne());

        $this->withSession(['auth.password_confirmed_at' => time()]);

        $component = Livewire::test(TwoFactorAuthenticationForm::class)
                ->call('enableTwoFactorAuthentication');

        static::assertNotNull($user->fresh()->two_factor_secret);

        $component->call('disableTwoFactorAuthentication');

        static::assertNull($user->fresh()->two_factor_secret);
    }
}
