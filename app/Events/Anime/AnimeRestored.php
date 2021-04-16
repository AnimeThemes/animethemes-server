<?php

namespace App\Events\Anime;

use App\Contracts\Events\CascadesRestoresEvent;
use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Discord\EmbedColor;
use App\Models\Entry;
use App\Models\Synonym;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

class AnimeRestored extends AnimeEvent implements CascadesRestoresEvent, DiscordMessageEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $anime = $this->getAnime();

        return DiscordMessage::create('', [
            'description' => "Anime '**{$anime->getName()}**' has been restored.",
            'color' => EmbedColor::GREEN,
        ]);
    }

    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel()
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
        $anime = $this->getAnime();

        $anime->synonyms()->withTrashed()->get()->each(function (Synonym $synonym) {
            Synonym::withoutEvents(function () use ($synonym) {
                $synonym->restore();
                $synonym->searchable();
            });
        });

        $anime->themes()->withTrashed()->get()->each(function (Theme $theme) {
            Theme::withoutEvents(function () use ($theme) {
                $theme->restore();
                $theme->searchable();
                $theme->entries()->withTrashed()->get()->each(function (Entry $entry) {
                    Entry::withoutEvents(function () use ($entry) {
                        $entry->restore();
                        $entry->searchable();
                        $entry->videos->each(function (Video $video) {
                            $video->searchable();
                        });
                    });
                });
            });
        });
    }
}
