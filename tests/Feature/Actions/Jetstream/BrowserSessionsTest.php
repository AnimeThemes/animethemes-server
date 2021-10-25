<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Jetstream;

use App\Models\Auth\User;
use Laravel\Jetstream\Http\Livewire\LogoutOtherBrowserSessionsForm;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class BrowserSessionsTest.
 */
class BrowserSessionsTest extends TestCase
{
    /**
     * Other browser sessions can be logged out.
     *
     * @return void
     */
    public function testOtherBrowserSessionsCanBeLoggedOut()
    {
        $this->actingAs(User::factory()->createOne());

        Livewire::test(LogoutOtherBrowserSessionsForm::class)
                ->set('password', 'password')
                ->call('logoutOtherBrowserSessions');
    }
}
