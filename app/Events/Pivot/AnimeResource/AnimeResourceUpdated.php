<?php

declare(strict_types=1);

namespace App\Events\Pivot\AnimeResource;

use App\Concerns\Services\Discord\HasAttributeUpdateEmbedFields;
use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Pivots\AnimeResource;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class AnimeResourceUpdated.
 */
class AnimeResourceUpdated extends AnimeResourceEvent implements DiscordMessageEvent
{
    use Dispatchable;
    use HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param AnimeResource $animeResource
     * @return void
     */
    public function __construct(AnimeResource $animeResource)
    {
        parent::__construct($animeResource);
        $this->initializeEmbedFields($animeResource);
    }

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $anime = $this->getAnime();
        $resource = $this->getResource();

        return DiscordMessage::create('', [
            'description' => "Resource '**{$resource->getName()}**' for Anime '**{$anime->getName()}**' has been updated.",
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
