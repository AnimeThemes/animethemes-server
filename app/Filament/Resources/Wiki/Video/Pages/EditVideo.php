<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video\Pages;

use App\Filament\HeaderActions\Models\Wiki\Video\BackfillAudioHeaderAction;
use App\Filament\HeaderActions\Storage\Wiki\Video\DeleteVideoHeaderAction;
use App\Filament\HeaderActions\Storage\Wiki\Video\MoveVideoHeaderAction;
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
                        ->authorize('create', VideoModel::class),

                    MoveVideoHeaderAction::make('move-video')
                        ->label(__('filament.actions.video.move.name'))
                        ->requiresConfirmation()
                        ->modalWidth(MaxWidth::FourExtraLarge)
                        ->authorize('create', VideoModel::class),
                    
                    DeleteVideoHeaderAction::make('delete-video')
                        ->label(__('filament.actions.video.delete.name'))
                        ->requiresConfirmation()
                        ->modalWidth(MaxWidth::FourExtraLarge)
                        ->authorize('forcedelete', VideoModel::class),
                ]),
            ],
        );
    }
}
