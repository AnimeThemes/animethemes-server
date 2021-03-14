<?php

namespace Tests\Feature\Events;

use App\Events\Synonym\SynonymCreated;
use App\Events\Synonym\SynonymDeleted;
use App\Events\Synonym\SynonymRestored;
use App\Events\Synonym\SynonymUpdated;
use App\Models\Anime;
use App\Models\Synonym;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

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
