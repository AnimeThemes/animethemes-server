<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Studio\Pages;

use App\Filament\HeaderActions\Models\Wiki\AttachImageHeaderAction;
use App\Filament\HeaderActions\Models\Wiki\Studio\AttachStudioResourceHeaderAction;
use App\Filament\HeaderActions\Models\Wiki\Studio\BackfillStudioHeaderAction;
use App\Filament\Resources\Wiki\Studio;
use App\Filament\Resources\Base\BaseEditResource;
use Filament\Actions\ActionGroup;
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
        return array_merge(
            parent::getHeaderActions(),
            [
                ActionGroup::make([
                    BackfillStudioHeaderAction::make('backfill-studio'),

                    AttachImageHeaderAction::make('attach-studio-image'),

                    AttachStudioResourceHeaderAction::make('attach-studio-resource'),
                ])
            ],
        );
    }
}
