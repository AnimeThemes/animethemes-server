<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\Pages;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\HeaderActions\Models\Wiki\Song\AttachSongResourceHeaderAction;
use App\Filament\Resources\Base\BaseEditResource;
use App\Filament\Resources\Wiki\Song;
use App\Models\Wiki\ExternalResource;
use Filament\Actions\ActionGroup;
use Filament\Support\Enums\MaxWidth;

/**
 * Class EditSong.
 */
class EditSong extends BaseEditResource
{
    protected static string $resource = Song::class;

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
            ResourceSite::SPOTIFY,
            ResourceSite::YOUTUBE_MUSIC,
            ResourceSite::YOUTUBE,
            ResourceSite::APPLE_MUSIC,
            ResourceSite::AMAZON_MUSIC,
        ];

        return array_merge(
            parent::getHeaderActions(),
            [
                ActionGroup::make([
                    AttachSongResourceHeaderAction::make('attach-song-resource')
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
