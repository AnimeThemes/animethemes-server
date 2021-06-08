<?php

declare(strict_types=1);

namespace App\Events\Theme;

use App\Contracts\Events\CascadesRestoresEvent;
use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Models\Entry;
use App\Models\Video;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class ThemeRestored.
 */
class ThemeRestored extends ThemeEvent implements CascadesRestoresEvent, DiscordMessageEvent
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
            'description' => "Theme '**{$theme->getName()}**' has been restored for Anime '**{$anime->getName()}**'.",
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
     * Perform cascading restores.
     *
     * @return void
     */
    public function cascadeRestores()
    {
        $theme = $this->getTheme();

        $theme->entries()->withTrashed()->get()->each(function (Entry $entry) {
            Entry::withoutEvents(function () use ($entry) {
                $entry->restore();
                $entry->searchable();
                $entry->videos->each(function (Video $video) {
                    $video->searchable();
                });
            });
        });
    }
}
