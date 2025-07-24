<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot\Wiki;

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\ArtistImage\ArtistImageCreated;
use App\Events\Pivot\Wiki\ArtistImage\ArtistImageDeleted;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class ArtistImageTest extends TestCase
{
    /**
     * When an Artist is attached to an Image or vice versa, a SendDiscordNotification job shall be dispatched.
     */
    public function testArtistImageCreatedSendsDiscordNotification(): void
    {
        $artist = Artist::factory()->createOne();
        $image = Image::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ArtistImageCreated::class);

        $artist->images()->attach($image);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Artist is detached from an Image or vice versa, a SendDiscordNotification job shall be dispatched.
     */
    public function testArtistImageDeletedSendsDiscordNotification(): void
    {
        $artist = Artist::factory()->createOne();
        $image = Image::factory()->createOne();

        $artist->images()->attach($image);

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ArtistImageDeleted::class);

        $artist->images()->detach($image);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
