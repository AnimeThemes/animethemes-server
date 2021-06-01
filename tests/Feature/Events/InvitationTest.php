<?php declare(strict_types=1);

namespace Events;

use App\Events\Invitation\InvitationCreated;
use App\Events\Invitation\InvitationDeleted;
use App\Events\Invitation\InvitationRestored;
use App\Events\Invitation\InvitationUpdated;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class InvitationTest
 * @package Events
 */
class InvitationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an Invitation is created, an InvitationCreated event shall be dispatched.
     *
     * @return void
     */
    public function testInvitationCreatedEventDispatched()
    {
        Event::fake(InvitationCreated::class);

        Invitation::factory()->create();

        Event::assertDispatched(InvitationCreated::class);
    }

    /**
     * When an Invitation is deleted, an InvitationDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testInvitationDeletedEventDispatched()
    {
        Event::fake(InvitationDeleted::class);

        $invitation = Invitation::factory()->create();

        $invitation->delete();

        Event::assertDispatched(InvitationDeleted::class);
    }

    /**
     * When an Invitation is restored, an InvitationRestored event shall be dispatched.
     *
     * @return void
     */
    public function testInvitationRestoredEventDispatched()
    {
        Event::fake(InvitationRestored::class);

        $invitation = Invitation::factory()->create();

        $invitation->restore();

        Event::assertDispatched(InvitationRestored::class);
    }

    /**
     * When an Invitation is updated, an InvitationUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testInvitationUpdatedEventDispatched()
    {
        Event::fake(InvitationUpdated::class);

        $invitation = Invitation::factory()->create();
        $changes = Invitation::factory()->make();

        $invitation->fill($changes->getAttributes());
        $invitation->save();

        Event::assertDispatched(InvitationUpdated::class);
    }
}
