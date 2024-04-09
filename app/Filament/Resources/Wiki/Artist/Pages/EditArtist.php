<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Artist\Pages;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\HeaderActions\Models\Wiki\Artist\AttachArtistResourceHeaderAction;
use App\Filament\Resources\Base\BaseEditResource;
use App\Filament\Resources\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use Filament\Actions\ActionGroup;
use Filament\Support\Enums\MaxWidth;

/**
 * Class EditArtist.
 */
class EditArtist extends BaseEditResource
{
    protected static string $resource = Artist::class;

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
            ResourceSite::MAL,
            ResourceSite::OFFICIAL_SITE,
            ResourceSite::SPOTIFY,
            ResourceSite::TWITTER,
            ResourceSite::YOUTUBE,
            ResourceSite::YOUTUBE_MUSIC,
            ResourceSite::WIKI,
        ];

        return array_merge(
            parent::getHeaderActions(),
            [
                ActionGroup::make([
                    AttachArtistResourceHeaderAction::make('attach-artist-resource')
                        ->label(__('filament.actions.models.wiki.attach_resource.name'))
                        ->icon('heroicon-o-queue-list')
                        ->sites($resourceSites)
                        ->requiresConfirmation()
                        ->modalWidth(MaxWidth::FourExtraLarge)
                        ->authorize('create', ExternalResource::class),
                ])
            ],
        );
    }
}
