<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme;

use App\Contracts\Events\DiscordMessageEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class ThemeCreated.
 */
class ThemeCreated extends ThemeEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
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
        $theme = $this->getTheme();
        $anime = $this->getAnime();

        return DiscordMessage::create('', [
            'description' => "Theme '**{$theme->getName()}**' has been created for Anime '**{$anime->getName()}**'.",
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

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices()
    {
        $theme = $this->getTheme()->load('animethemeentries.videos');

        $theme->animethemeentries->each(function (AnimeThemeEntry $entry) {
            $entry->searchable();
            $entry->videos->each(fn (Video $video) => $video->searchable());
        });
    }
}
