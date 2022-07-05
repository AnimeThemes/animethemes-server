<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki\Anime;

use App\Events\Wiki\Anime\Synonym\SynonymCreated;
use App\Events\Wiki\Anime\Synonym\SynonymDeleted;
use App\Events\Wiki\Anime\Synonym\SynonymRestored;
use App\Events\Wiki\Anime\Synonym\SynonymUpdated;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Support\Arr;
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
    public function testSynonymCreatedEventDispatched(): void
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
    public function testSynonymDeletedEventDispatched(): void
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
    public function testSynonymRestoredEventDispatched(): void
    {
        Event::fake();

        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        $synonym->restore();

        Event::assertDispatched(SynonymRestored::class);
    }

    /**
     * When a Synonym is restored, a SynonymUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function testSynonymRestoresQuietly(): void
    {
        Event::fake();

        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        $synonym->restore();

        Event::assertNotDispatched(SynonymUpdated::class);
    }

    /**
     * When a Synonym is updated, a SynonymUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testSynonymUpdatedEventDispatched(): void
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

    /**
     * The SynonymUpdated event shall contain embed fields.
     *
     * @return void
     */
    public function testSynonymUpdatedEventEmbedFields(): void
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

        Event::assertDispatched(SynonymUpdated::class, function (SynonymUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
