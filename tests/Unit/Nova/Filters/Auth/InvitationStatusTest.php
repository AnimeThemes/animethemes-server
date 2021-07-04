<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Filters\Auth;

use App\Enums\Models\Auth\InvitationStatus;
use App\Models\Auth\Invitation;
use App\Nova\Filters\Auth\InvitationStatusFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class InvitationStatusTest.
 */
class InvitationStatusTest extends TestCase
{
    use NovaFilterTest;
    use RefreshDatabase;
    use WithFaker;

    /**
     * The Invitation Status Filter shall be a select filter.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter()
    {
        static::novaFilter(InvitationStatusFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Invitation Status Filter shall have an option for each InvitationStatus instance.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testOptions()
    {
        $filter = static::novaFilter(InvitationStatusFilter::class);

        foreach (InvitationStatus::getInstances() as $status) {
            $filter->assertHasOption($status->description);
        }
    }

    /**
     * The Invitation Status Filter shall filter Invitations By Status.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter()
    {
        $status = InvitationStatus::getRandomInstance();

        Invitation::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = static::novaFilter(InvitationStatusFilter::class);

        $response = $filter->apply(Invitation::class, $status->value);

        $filteredInvitations = Invitation::where('status', $status->value)->get();
        foreach ($filteredInvitations as $filteredInvitation) {
            $response->assertContains($filteredInvitation);
        }
        $response->assertCount($filteredInvitations->count());
    }
}
