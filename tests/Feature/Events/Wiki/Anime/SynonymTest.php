<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki\Anime;

use App\Events\Wiki\Anime\Synonym\SynonymCreated;
use App\Events\Wiki\Anime\Synonym\SynonymDeleted;
use App\Events\Wiki\Anime\Synonym\SynonymRestored;
use App\Events\Wiki\Anime\Synonym\SynonymUpdated;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class SynonymTest.
 */
class SynonymTest extends TestCase
{
    /**
     * When a Synonym is created, a SynonymCreated event shall be dispatched.
     *
     * @return void
     */
    public function testSynonymCreatedEventDispatched()
    {
        Event::fake();

        AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

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

        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

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

        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

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

        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        $changes = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->makeOne();

        $synonym->fill($changes->getAttributes());
        $synonym->save();

        Event::assertDispatched(SynonymUpdated::class);
    }
}
