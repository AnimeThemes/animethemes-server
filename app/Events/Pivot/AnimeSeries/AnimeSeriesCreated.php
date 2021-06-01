<?php declare(strict_types=1);

namespace App\Events\Pivot\AnimeSeries;

use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Discord\EmbedColor;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class AnimeSeriesCreated
 * @package App\Events\Pivot\AnimeSeries
 */
class AnimeSeriesCreated extends AnimeSeriesEvent implements DiscordMessageEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $anime = $this->getAnime();
        $series = $this->getSeries();

        return DiscordMessage::create('', [
            'description' => "Anime '**{$anime->getName()}**' has been attached to Series '**{$series->getName()}**'.",
            'color' => EmbedColor::GREEN,
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
