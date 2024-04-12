<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Audio\Pages;

use App\Filament\HeaderActions\Models\Wiki\Audio\AttachAudioToRelatedVideosHeaderAction;
use App\Filament\Resources\Wiki\Audio;
use App\Filament\Resources\Base\BaseEditResource;
use App\Models\Wiki\Video;

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
                AttachAudioToRelatedVideosHeaderAction::make('attach-audio-related-video')
                    ->label('filament.actions.audio.attach_related_videos.name')
                    ->requiresConfirmation()
                    ->authorize('update', Video::class),
            ],
        );
    }
}
