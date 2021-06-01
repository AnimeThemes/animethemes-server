<?php declare(strict_types=1);

namespace App\Events\Theme;

use App\Concerns\Discord\HasAttributeUpdateEmbedFields;
use App\Contracts\Events\DiscordMessageEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Enums\Discord\EmbedColor;
use App\Models\Entry;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class ThemeUpdated
 * @package App\Events\Theme
 */
class ThemeUpdated extends ThemeEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
{
    use Dispatchable;
    use HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param Theme $theme
     * @return void
     */
    public function __construct(Theme $theme)
    {
        parent::__construct($theme);
        $this->initializeEmbedFields($theme);
    }

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $theme = $this->getTheme();
        $anime = $this->getAnime();

        return DiscordMessage::create('', [
            'description' => "Theme '**{$theme->getName()}**' has been updated for Anime '**{$anime->getName()}**'.",
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

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices()
    {
        $theme = $this->getTheme()->load('entries.videos');

        $theme->entries->each(function (Entry $entry) {
            $entry->searchable();
            $entry->videos->each(function (Video $video) {
                $video->searchable();
            });
        });
    }
}
