<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Audio\Pages;

use App\Filament\HeaderActions\Models\Wiki\Audio\AttachAudioToRelatedVideosHeaderAction;
use App\Filament\HeaderActions\Storage\Wiki\Audio\DeleteAudioHeaderAction;
use App\Filament\HeaderActions\Storage\Wiki\Audio\MoveAudioHeaderAction;
use App\Filament\Resources\Wiki\Audio;
use App\Filament\Resources\Base\BaseEditResource;
use Filament\Actions\ActionGroup;

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
                    MoveAudioHeaderAction::make('move-audio'),
                    
                    DeleteAudioHeaderAction::make('delete-audio'),

                    AttachAudioToRelatedVideosHeaderAction::make('attach-audio-related-video'),
                ]),
            ],
        );
    }
}
