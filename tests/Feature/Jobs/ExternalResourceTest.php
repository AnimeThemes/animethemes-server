<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SendDiscordNotification;
use App\Models\ExternalResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ExternalResourceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * When a resource is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testResourceCreatedSendsDiscordNotification()
    {
        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        ExternalResource::factory()->create();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When a resource is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testResourceDeletedSendsDiscordNotification()
    {
        $resource = ExternalResource::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $resource->delete();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When a resource is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testResourceUpdatedSendsDiscordNotification()
    {
        $resource = ExternalResource::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $changes = ExternalResource::factory()->make();

        $resource->fill($changes->getAttributes());
        $resource->save();

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
