<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Pages;

use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\HeaderActions\Discord\DiscordThreadHeaderAction;
use App\Filament\HeaderActions\Models\Wiki\Anime\AttachAnimeImageHeaderAction;
use App\Filament\HeaderActions\Models\Wiki\Anime\AttachAnimeResourceHeaderAction;
use App\Filament\Resources\Wiki\Anime;
use App\Filament\Resources\Base\BaseEditResource;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Video;
use Filament\Actions\ActionGroup;
use Filament\Support\Enums\MaxWidth;

/**
 * Class EditAnime.
 */
class EditAnime extends BaseEditResource
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
        $resourceSites = [
            ResourceSite::ANIDB,
            ResourceSite::ANILIST,
            ResourceSite::ANIME_PLANET,
            ResourceSite::ANN,
            ResourceSite::KITSU,
            ResourceSite::MAL,
            ResourceSite::OFFICIAL_SITE,
            ResourceSite::TWITTER,
            ResourceSite::YOUTUBE,
            ResourceSite::WIKI,
        ];

        $streamingResourceSites = [
            ResourceSite::CRUNCHYROLL,
            ResourceSite::HIDIVE,
            ResourceSite::NETFLIX,
            ResourceSite::DISNEY_PLUS,
            ResourceSite::HULU,
            ResourceSite::AMAZON_PRIME_VIDEO,
        ];

        $facets = [
            ImageFacet::COVER_SMALL,
            ImageFacet::COVER_LARGE,
        ];

        return array_merge(
            parent::getHeaderActions(),
            [
                ActionGroup::make([
                    DiscordThreadHeaderAction::make('discord-thread-header')
                        ->label(__('filament.actions.anime.discord.thread.name'))
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->requiresConfirmation()
                        ->authorize('create', Video::class),

                    AttachAnimeImageHeaderAction::make('attach-anime-image')
                        ->label(__('filament.actions.models.wiki.attach_image.name'))
                        ->icon('heroicon-o-photo')
                        ->facets($facets)
                        ->requiresConfirmation()
                        ->authorize('create', Image::class),

                    AttachAnimeResourceHeaderAction::make('attach-anime-resource')
                        ->label(__('filament.actions.models.wiki.attach_resource.name'))
                        ->icon('heroicon-o-queue-list')
                        ->sites($resourceSites)
                        ->requiresConfirmation()
                        ->modalWidth(MaxWidth::FourExtraLarge)
                        ->authorize('create', ExternalResource::class),

                    AttachAnimeResourceHeaderAction::make('attach-anime-streaming-resource')
                        ->label(__('filament.actions.models.wiki.attach_streaming_resource.name'))
                        ->icon('heroicon-o-tv')
                        ->sites($streamingResourceSites)
                        ->requiresConfirmation()
                        ->modalWidth(MaxWidth::FourExtraLarge)
                        ->authorize('create', ExternalResource::class),
                ]),
            ],
        );
    }
}
