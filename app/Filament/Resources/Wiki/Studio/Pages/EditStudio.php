<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Studio\Pages;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\HeaderActions\Models\Wiki\Studio\AttachStudioResourceHeaderAction;
use App\Filament\Resources\Wiki\Studio;
use App\Filament\Resources\Base\BaseEditResource;
use App\Models\Wiki\ExternalResource;
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
