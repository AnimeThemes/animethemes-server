<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Studio\Pages;

use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\HeaderActions\Models\Wiki\Studio\AttachStudioImageHeaderAction;
use App\Filament\HeaderActions\Models\Wiki\Studio\AttachStudioResourceHeaderAction;
use App\Filament\HeaderActions\Models\Wiki\Studio\BackfillStudioHeaderAction;
use App\Filament\Resources\Wiki\Studio;
use App\Filament\Resources\Base\BaseEditResource;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio as StudioModel;
use Filament\Actions\ActionGroup;
use Filament\Support\Enums\MaxWidth;

/**
 * Class EditStudio.
 */
class EditStudio extends BaseEditResource
{
    protected static string $resource = Studio::class;

    /**
     * Get the header actions available.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getHeaderActions(): array
    {
        $facets = [
            ImageFacet::COVER_SMALL,
            ImageFacet::COVER_LARGE,
        ];

        $resourceSites = [
            ResourceSite::ANIDB,
            ResourceSite::ANILIST,
            ResourceSite::ANIME_PLANET,
            ResourceSite::ANN,
            ResourceSite::MAL,
        ];

        return array_merge(
            parent::getHeaderActions(),
            [
                ActionGroup::make([
                    BackfillStudioHeaderAction::make('backfill-studio')
                        ->label(__('filament.actions.studio.backfill.name'))
                        ->requiresConfirmation()
                        ->modalWidth(MaxWidth::FourExtraLarge)
                        ->authorize('update', StudioModel::class),

                    AttachStudioImageHeaderAction::make('attach-studio-image')
                        ->label(__('filament.actions.models.wiki.attach_image.name'))
                        ->icon('heroicon-o-photo')
                        ->facets($facets)
                        ->requiresConfirmation()
                        ->authorize('create', Image::class),

                    AttachStudioResourceHeaderAction::make('attach-studio-resource')
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
