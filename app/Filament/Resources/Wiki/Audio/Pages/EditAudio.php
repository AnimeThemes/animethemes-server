<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Audio\Pages;

use App\Filament\HeaderActions\Models\Wiki\Audio\AttachAudioToRelatedVideosHeaderAction;
use App\Filament\HeaderActions\Storage\Wiki\Audio\DeleteAudioHeaderAction;
use App\Filament\HeaderActions\Storage\Wiki\Audio\MoveAudioHeaderAction;
use App\Filament\Resources\Wiki\Audio;
use App\Filament\Resources\Base\BaseEditResource;
use App\Models\Wiki\Audio as AudioModel;
use App\Models\Wiki\Video;
use Filament\Actions\ActionGroup;
use Filament\Support\Enums\MaxWidth;

/**
 * Class EditAudio.
 */
class EditAudio extends BaseEditResource
{
    protected static string $resource = Audio::class;

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
                    MoveAudioHeaderAction::make('move-audio')
                        ->label(__('filament.actions.audio.move.name'))
                        ->requiresConfirmation()
                        ->modalWidth(MaxWidth::FourExtraLarge)
                        ->authorize('create', AudioModel::class),
                    
                    DeleteAudioHeaderAction::make('delete-audio')
                        ->label(__('filament.actions.audio.delete.name'))
                        ->requiresConfirmation()
                        ->modalWidth(MaxWidth::FourExtraLarge)
                        ->authorize('delete', AudioModel::class),

                    AttachAudioToRelatedVideosHeaderAction::make('attach-audio-related-video')
                        ->label(__('filament.actions.audio.attach_related_videos.name'))
                        ->requiresConfirmation()
                        ->authorize('update', Video::class),
                ]),
            ],
        );
    }
}
