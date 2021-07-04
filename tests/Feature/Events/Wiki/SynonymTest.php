<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki;

use App\Events\Wiki\Synonym\SynonymCreated;
use App\Events\Wiki\Synonym\SynonymDeleted;
use App\Events\Wiki\Synonym\SynonymRestored;
use App\Events\Wiki\Synonym\SynonymUpdated;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Synonym;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class SynonymTest.
 */
class SynonymTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When a Synonym is created, a SynonymCreated event shall be dispatched.
     *
     * @return void
     */
    public function testSynonymCreatedEventDispatched()
    {
        Event::fake();

        Synonym::factory()
            ->for(Anime::factory())
            ->create();

        Event::assertDispatched(SynonymCreated::class);
    }

    /**
     * When a Synonym is deleted, a SynonymDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testSynonymDeletedEventDispatched()
    {
        Event::fake();

        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();

        $synonym->delete();

        Event::assertDispatched(SynonymDeleted::class);
    }

    /**
     * When a Synonym is restored, a SynonymRestored event shall be dispatched.
     *
     * @return void
     */
    public function testSynonymRestoredEventDispatched()
    {
        Event::fake();

        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();

        $synonym->restore();

        Event::assertDispatched(SynonymRestored::class);
    }

    /**
     * When a Synonym is updated, a SynonymUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testSynonymUpdatedEventDispatched()
    {
        Event::fake();

        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();
        $changes = Synonym::factory()
            ->for(Anime::factory())
            ->create();

        $synonym->fill($changes->getAttributes());
        $synonym->save();

        Event::assertDispatched(SynonymUpdated::class);
    }
}
