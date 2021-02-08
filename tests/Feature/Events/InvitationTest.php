<?php

namespace Tests\Feature\Events;

use App\Events\Invitation\InvitationCreated;
use App\Events\Invitation\InvitationDeleted;
use App\Events\Invitation\InvitationUpdated;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
