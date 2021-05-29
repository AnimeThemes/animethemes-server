<?php

namespace Tests\Unit\Nova\Filters;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use App\Nova\Filters\InvitationStatusFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class InvitationStatusTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker;

    /**
     * The Invitation Status Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(InvitationStatusFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Invitation Status Filter shall have an option for each InvitationStatus instance.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(InvitationStatusFilter::class);

        foreach (InvitationStatus::getInstances() as $status) {
            $filter->assertHasOption($status->description);
        }
    }

    /**
     * The Invitation Status Filter shall filter Invitations By Status.
     *
     * @return void
     */
    public function testFilter()
    {
        $status = InvitationStatus::getRandomInstance();

        Invitation::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = $this->novaFilter(InvitationStatusFilter::class);

        $response = $filter->apply(Invitation::class, $status->value);

        $filtered_invitations = Invitation::where('status', $status->value)->get();
        foreach ($filtered_invitations as $filtered_invitation) {
            $response->assertContains($filtered_invitation);
        }
        $response->assertCount($filtered_invitations->count());
    }
}
