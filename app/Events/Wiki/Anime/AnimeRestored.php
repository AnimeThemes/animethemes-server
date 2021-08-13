<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime;

use App\Contracts\Events\CascadesRestoresEvent;
use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Models\Wiki\Anime\Theme\Entry;
use App\Models\Wiki\Anime\Synonym;
use App\Models\Wiki\Anime\Theme;
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

        $anime->synonyms()->withoutGlobalScope(SoftDeletingScope::class)->get()->each(function (Synonym $synonym) {
            Synonym::withoutEvents(function () use ($synonym) {
                $synonym->restore();
                $synonym->searchable();
            });
        });

        $anime->themes()->withoutGlobalScope(SoftDeletingScope::class)->get()->each(function (Theme $theme) {
            Theme::withoutEvents(function () use ($theme) {
                $theme->restore();
                $theme->searchable();
                $theme->entries()->withoutGlobalScope(SoftDeletingScope::class)->get()->each(function (Entry $entry) {
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
