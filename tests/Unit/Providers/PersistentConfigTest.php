<?php

namespace Tests\Unit\Providers;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class PersistentConfigTest extends TestCase
{
    /**
     * Allow Video Streams shall be persisted to the database.
     *
     * @return void
     */
    public function testAllowVideoStreams()
    {
        $persisted_items = Config::getItems();

        $this->assertArrayHasKey('app.allow_video_streams', $persisted_items);
    }

    /**
     * Allow Discord Notifications shall be persisted to the database.
     *
     * @return void
     */
    public function testAllowDiscordNotifications()
    {
        $persisted_items = Config::getItems();

        $this->assertArrayHasKey('app.allow_discord_notifications', $persisted_items);
    }
}
