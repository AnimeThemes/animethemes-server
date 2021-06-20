<?php

declare(strict_types=1);

namespace Jetstream;

use App\Models\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Http\Livewire\LogoutOtherBrowserSessionsForm;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class BrowserSessionsTest.
 */
class BrowserSessionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Other browser sessions can be logged out.
     *
     * @return void
     */
    public function testOtherBrowserSessionsCanBeLoggedOut()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(LogoutOtherBrowserSessionsForm::class)
                ->set('password', 'password')
                ->call('logoutOtherBrowserSessions');
    }
}
