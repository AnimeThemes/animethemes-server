<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video\Pages;

use App\Filament\HeaderActions\Models\Wiki\Video\BackfillAudioHeaderAction;
use App\Filament\Resources\Wiki\Video;
use App\Filament\Resources\Base\BaseEditResource;
use App\Models\Wiki\Video as VideoModel;
use Filament\Actions\ActionGroup;
use Filament\Support\Enums\MaxWidth;

/**
 * Class EditVideo.
 */
class EditVideo extends BaseEditResource
{
    protected static string $resource = Video::class;

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
                    BackfillAudioHeaderAction::make('backfill-audio')
                        ->label(__('filament.actions.video.backfill.name'))
                        ->requiresConfirmation()
                        ->modalWidth(MaxWidth::TwoExtraLarge)
                        ->authorize('update', VideoModel::class),
                ]),
            ],
        );
    }
}
