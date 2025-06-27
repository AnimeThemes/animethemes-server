<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot\Wiki;

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\AnimeImage\AnimeImageCreated;
use App\Events\Pivot\Wiki\AnimeImage\AnimeImageDeleted;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class AnimeImageTest.
 */
class AnimeImageTest extends TestCase
{
    /**
     * When an Anime is attached to an Image or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_anime_image_created_sends_discord_notification(): void
    {
        $anime = Anime::factory()->createOne();
        $image = Image::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnimeImageCreated::class);

        $anime->images()->attach($image);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Anime is detached from an Image or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_anime_image_deleted_sends_discord_notification(): void
    {
        $anime = Anime::factory()->createOne();
        $image = Image::factory()->createOne();

        $anime->images()->attach($image);

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnimeImageDeleted::class);

        $anime->images()->detach($image);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
