<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot\Wiki;

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\AnimeSeries\AnimeSeriesCreated;
use App\Events\Pivot\Wiki\AnimeSeries\AnimeSeriesDeleted;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class AnimeSeriesTest extends TestCase
{
    /**
     * When an Anime is attached to a Series or vice versa, a SendDiscordNotification job shall be dispatched.
     */
    public function testAnimeSeriesCreatedSendsDiscordNotification(): void
    {
        $anime = Anime::factory()->createOne();
        $series = Series::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnimeSeriesCreated::class);

        $anime->series()->attach($series);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Anime is detached from a Series or vice versa, a SendDiscordNotification job shall be dispatched.
     */
    public function testAnimeSeriesDeletedSendsDiscordNotification(): void
    {
        $anime = Anime::factory()->createOne();
        $series = Series::factory()->createOne();

        $anime->series()->attach($series);

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnimeSeriesDeleted::class);

        $anime->series()->detach($series);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
