<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Synonym;

use App\Contracts\Events\DiscordMessageEvent;
use App\Contracts\Events\NovaNotificationEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Nova\Resources\Wiki\Anime\Synonym as SynonymResource;
use App\Services\Nova\NovaQueries;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Laravel\Nova\Notifications\NovaNotification;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class SynonymDeleted.
 */
class SynonymDeleted extends SynonymEvent implements DiscordMessageEvent, NovaNotificationEvent, UpdateRelatedIndicesEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $synonym = $this->getSynonym();
        $anime = $this->getAnime();

        return DiscordMessage::create('', [
            'description' => "Synonym '**{$synonym->getName()}**' has been deleted for Anime '**{$anime->getName()}**'.",
            'color' => EmbedColor::RED,
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
    public function updateRelatedIndices(): void
    {
        $anime = $this->getAnime()->load(Anime::RELATION_VIDEOS);

        $anime->searchable();
        $anime->animethemes->each(function (AnimeTheme $theme) {
            $theme->searchable();
            $theme->animethemeentries->each(function (AnimeThemeEntry $entry) {
                $entry->searchable();
                $entry->videos->each(fn (Video $video) => $video->searchable());
            });
        });
    }

    /**
     * Determine if the notifications should be sent.
     *
     * @return bool
     */
    public function shouldSend(): bool
    {
        $synonym = $this->getSynonym();

        return ! $synonym->isForceDeleting();
    }

    /**
     * Get the nova notification.
     *
     * @return NovaNotification
     */
    public function getNotification(): NovaNotification
    {
        $synonym = $this->getSynonym();
        $anime = $this->getAnime();

        $uriKey = SynonymResource::uriKey();

        return NovaNotification::make()
            ->icon('flag')
            ->message("Synonym '{$synonym->getName()}' has been deleted for Anime '{$anime->getName()}'. It will be automatically pruned in one week. Please review.")
            ->type(NovaNotification::INFO_TYPE)
            ->url("/resources/$uriKey/{$synonym->getKey()}");
    }

    /**
     * Get the users to notify.
     *
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return NovaQueries::admins();
    }
}
