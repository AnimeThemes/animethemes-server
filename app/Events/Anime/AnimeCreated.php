<?php

declare(strict_types=1);

namespace App\Events\Anime;

use App\Contracts\Events\DiscordMessageEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Models\Entry;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class AnimeCreated.
 */
class AnimeCreated extends AnimeEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
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

        return DiscordMessage::create('', [
            'description' => "Anime '**{$anime->getName()}**' has been created.",
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
        $anime = $this->getAnime()->load('themes.entries.videos');

        $anime->themes->each(function (Theme $theme) {
            $theme->searchable();
            $theme->entries->each(function (Entry $entry) {
                $entry->searchable();
                $entry->videos->each(function (Video $video) {
                    $video->searchable();
                });
            });
        });
    }
}
