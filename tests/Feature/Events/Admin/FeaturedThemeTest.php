<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Admin;

use App\Events\Admin\FeaturedTheme\FeaturedThemeCreated;
use App\Events\Admin\FeaturedTheme\FeaturedThemeDeleted;
use App\Events\Admin\FeaturedTheme\FeaturedThemeUpdated;
use App\Models\Admin\FeaturedTheme;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class FeaturedThemeTest extends TestCase
{
    /**
     * When a Featured Theme is created, a FeaturedThemeCreated event shall be dispatched.
     */
    public function testFeaturedThemeCreatedEventDispatched(): void
    {
        FeaturedTheme::factory()->create();

        Event::assertDispatched(FeaturedThemeCreated::class);
    }

    /**
     * When a Featured Theme is deleted, a FeaturedThemeDeleted event shall be dispatched.
     */
    public function testFeaturedThemeDeletedEventDispatched(): void
    {
        $featuredTheme = FeaturedTheme::factory()->create();

        $featuredTheme->delete();

        Event::assertDispatched(FeaturedThemeDeleted::class);
    }

    /**
     * When a Featured Theme is updated, a FeaturedThemeUpdated event shall be dispatched.
     */
    public function testFeaturedThemeUpdatedEventDispatched(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();
        $changes = FeaturedTheme::factory()->makeOne();

        $featuredTheme->fill($changes->getAttributes());
        $featuredTheme->save();

        Event::assertDispatched(FeaturedThemeUpdated::class);
    }

    /**
     * The FeaturedThemeUpdated event shall contain embed fields.
     */
    public function testFeaturedThemeUpdatedEventEmbedFields(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();
        $changes = FeaturedTheme::factory()->makeOne();

        $featuredTheme->fill($changes->getAttributes());
        $featuredTheme->save();

        Event::assertDispatched(FeaturedThemeUpdated::class, function (FeaturedThemeUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
