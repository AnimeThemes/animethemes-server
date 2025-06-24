<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Pages;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Actions\Models\Wiki\Anime\AttachAnimeResourceAction;
use App\Filament\Actions\Models\Wiki\Anime\BackfillAnimeAction;
use App\Filament\Actions\Models\Wiki\Anime\DiscordThreadAction;
use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Wiki\Anime;
use Filament\Actions\ActionGroup;

/**
 * Class ViewAnime.
 */
class ViewAnime extends BaseViewResource
{
    protected static string $resource = Anime::class;

    /**
     * Get the header actions available.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getHeaderActions(): array
    {
        $streamingResourceSites = [
            ResourceSite::CRUNCHYROLL,
            ResourceSite::HIDIVE,
            ResourceSite::NETFLIX,
            ResourceSite::DISNEY_PLUS,
            ResourceSite::HULU,
            ResourceSite::AMAZON_PRIME_VIDEO,
        ];

        return [
            ...parent::getHeaderActions(),

            ActionGroup::make([
                DiscordThreadAction::make('discord-thread-header'),

                BackfillAnimeAction::make('backfill-anime'),

                AttachAnimeResourceAction::make('attach-anime-resource'),

                AttachAnimeResourceAction::make('attach-anime-streaming-resource')
                    ->label(__('filament.actions.models.wiki.attach_streaming_resource.name'))
                    ->icon(__('filament-icons.actions.anime.attach_streaming_resource'))
                    ->sites($streamingResourceSites),
            ]),
        ];
    }
}
