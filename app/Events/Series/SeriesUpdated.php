<?php declare(strict_types=1);

namespace App\Events\Series;

use App\Concerns\Discord\HasAttributeUpdateEmbedFields;
use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Discord\EmbedColor;
use App\Models\Series;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class SeriesUpdated
 * @package App\Events\Series
 */
class SeriesUpdated extends SeriesEvent implements DiscordMessageEvent
{
    use Dispatchable;
    use HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param Series $series
     * @return void
     */
    public function __construct(Series $series)
    {
        parent::__construct($series);
        $this->initializeEmbedFields($series);
    }

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $series = $this->getSeries();

        return DiscordMessage::create('', [
            'description' => "Series '**{$series->getName()}**' has been updated.",
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
