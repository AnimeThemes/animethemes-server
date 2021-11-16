<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime;

use App\Contracts\Events\CascadesRestoresEvent;
use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class AnimeRestored.
 */
class AnimeRestored extends AnimeEvent implements CascadesRestoresEvent, DiscordMessageEvent
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
            'description' => "Anime '**{$anime->getName()}**' has been restored.",
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
        $anime = $this->getAnime();

        $anime->animesynonyms()->withoutGlobalScope(SoftDeletingScope::class)->get()->each(function (AnimeSynonym $synonym) {
            AnimeSynonym::withoutEvents(function () use ($synonym) {
                $synonym->restore();
                $synonym->searchable();
            });
        });

        $anime->animethemes()->withoutGlobalScope(SoftDeletingScope::class)->get()->each(function (AnimeTheme $theme) {
            AnimeTheme::withoutEvents(function () use ($theme) {
                $theme->restore();
                $theme->searchable();
                $theme->animethemeentries()->withoutGlobalScope(SoftDeletingScope::class)->get()->each(function (AnimeThemeEntry $entry) {
                    AnimeThemeEntry::withoutEvents(function () use ($entry) {
                        $entry->restore();
                        $entry->searchable();
                        $entry->videos->each(fn (Video $video) => $video->searchable());
                    });
                });
            });
        });
    }
}
