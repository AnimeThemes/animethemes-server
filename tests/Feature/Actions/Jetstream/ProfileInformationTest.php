<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Jetstream;

use App\Models\Auth\User;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class ProfileInformationTest.
 */
class ProfileInformationTest extends TestCase
{
    /**
     * Current profile information is available.
     *
     * @return void
     *
     * @noinspection PhpUndefinedFieldInspection
     */
    public function testCurrentProfileInformationIsAvailable()
    {
        $this->actingAs($user = User::factory()->createOne());

        $component = Livewire::test(UpdateProfileInformationForm::class);

        static::assertEquals($user->name, $component->state['name']);
        static::assertEquals($user->email, $component->state['email']);
    }

    /**
     * Profile information can be updated.
     *
     * @return void
     */
    public function testProfileInformationCanBeUpdated()
    {
        $this->actingAs($user = User::factory()->createOne());

        Livewire::test(UpdateProfileInformationForm::class)
                ->set('state', ['name' => 'Test Name', 'email' => 'test@example.com'])
                ->call('updateProfileInformation');

        static::assertEquals('Test Name', $user->fresh()->name);
        static::assertEquals('test@example.com', $user->fresh()->email);
    }
}
