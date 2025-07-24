<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Pivot\Wiki;

use App\Events\Pivot\Wiki\AnimeStudio\AnimeStudioCreated;
use App\Events\Pivot\Wiki\AnimeStudio\AnimeStudioDeleted;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AnimeStudioTest extends TestCase
{
    /**
     * When an Anime is attached to a Studio or vice versa, an AnimeStudioCreated event shall be dispatched.
     */
    public function testAnimeStudioCreatedEventDispatched(): void
    {
        $anime = Anime::factory()->createOne();
        $studio = Studio::factory()->createOne();

        $anime->studios()->attach($studio);

        Event::assertDispatched(AnimeStudioCreated::class);
    }

    /**
     * When an Anime is detached from a Studio or vice versa, an AnimeStudioDeleted event shall be dispatched.
     */
    public function testAnimeStudioDeletedEventDispatched(): void
    {
        $anime = Anime::factory()->createOne();
        $studio = Studio::factory()->createOne();

        $anime->studios()->attach($studio);
        $anime->studios()->detach($studio);

        Event::assertDispatched(AnimeStudioDeleted::class);
    }
}
