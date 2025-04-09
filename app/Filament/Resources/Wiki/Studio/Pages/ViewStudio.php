<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Studio\Pages;

use App\Filament\HeaderActions\Models\Wiki\Studio\AttachStudioResourceHeaderAction;
use App\Filament\HeaderActions\Models\Wiki\Studio\BackfillStudioHeaderAction;
use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Wiki\Studio;
use Filament\Actions\ActionGroup;

/**
 * Class ViewStudio.
 */
class ViewStudio extends BaseViewResource
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
        return [
            ...parent::getHeaderActions(),

            ActionGroup::make([
                BackfillStudioHeaderAction::make('backfill-studio'),

                AttachStudioResourceHeaderAction::make('attach-studio-resource'),
            ])
        ];
    }
}
