<?php

declare(strict_types=1);

namespace App\Events\Wiki\ExternalResource;

use App\Concerns\Services\Discord\HasAttributeUpdateEmbedFields;
use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Models\Wiki\ExternalResource;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class ExternalResourceUpdated.
 */
class ExternalResourceUpdated extends ExternalResourceEvent implements DiscordMessageEvent
{
    use Dispatchable;
    use HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param ExternalResource $resource
     * @return void
     */
    public function __construct(ExternalResource $resource)
    {
        parent::__construct($resource);
        $this->initializeEmbedFields($resource);
    }

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $resource = $this->getResource();

        return DiscordMessage::create('', [
            'description' => "Resource '**{$resource->getName()}**' has been updated.",
            'fields' => $this->getEmbedFields(),
            'color' => EmbedColor::YELLOW,
        ]);
    }

    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel(): string
    {
        return Config::get('services.discord.db_updates_discord_channel');
    }
}