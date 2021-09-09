<?php

declare(strict_types=1);

namespace App\Events\Wiki\Studio;

use App\Concerns\Services\Discord\HasAttributeUpdateEmbedFields;
use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Models\Wiki\Studio;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class StudioUpdated.
 */
class StudioUpdated extends StudioEvent implements DiscordMessageEvent
{
    use Dispatchable;
    use HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param  Studio  $studio
     * @return void
     */
    public function __construct(Studio $studio)
    {
        parent::__construct($studio);
        $this->initializeEmbedFields($studio);
    }

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $studio = $this->getStudio();

        return DiscordMessage::create('', [
            'description' => "Studio '**{$studio->getName()}**' has been updated.",
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
