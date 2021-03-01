<?php

namespace Tests\Feature\Events;

use App\Events\Anime\AnimeCreated;
use App\Events\Anime\AnimeDeleted;
use App\Events\Anime\AnimeUpdated;
use App\Models\Anime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AnimeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * When an Anime is created, an AnimeCreated event shall be dispatched.
     *
     * @return void
     */
    public function testAnimeCreatedEventDispatched()
    {
        Event::fake();

        Anime::factory()->create();

        Event::assertDispatched(AnimeCreated::class);
    }

    /**
     * When an Anime is deleted, an AnimeDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testAnimeDeletedEventDispatched()
    {
        Event::fake();

        $anime = Anime::factory()->create();

        $anime->delete();

        Event::assertDispatched(AnimeDeleted::class);
    }

    /**
     * When an Anime is updated, an AnimeUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testAnimeUpdatedEventDispatched()
    {
        Event::fake();

        $anime = Anime::factory()->create();
        $changes = Anime::factory()->make();

        $anime->fill($changes->getAttributes());
        $anime->save();

        Event::assertDispatched(AnimeUpdated::class);
    }
}