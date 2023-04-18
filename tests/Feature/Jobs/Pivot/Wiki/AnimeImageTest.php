<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot\Wiki;

use App\Constants\Config\FlagConstants;
use App\Events\Pivot\Wiki\AnimeImage\AnimeImageCreated;
use App\Events\Pivot\Wiki\AnimeImage\AnimeImageDeleted;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
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
    public function testAnimeImageCreatedSendsDiscordNotification(): void
    {
        $anime = Anime::factory()->createOne();
        $image = Image::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
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
    public function testAnimeImageDeletedSendsDiscordNotification(): void
    {
        $anime = Anime::factory()->createOne();
        $image = Image::factory()->createOne();

        $anime->images()->attach($image);

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnimeImageDeleted::class);

        $anime->images()->detach($image);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
