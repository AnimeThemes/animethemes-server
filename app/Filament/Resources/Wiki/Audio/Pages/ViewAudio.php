<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Audio\Pages;

use App\Filament\Actions\Storage\Wiki\Audio\DeleteAudioAction;
use App\Filament\Actions\Storage\Wiki\Audio\MoveAudioAction;
use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Wiki\Audio;
use Filament\Actions\ActionGroup;

/**
 * Class ViewAudio.
 */
class ViewAudio extends BaseViewResource
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
        return [
            ...parent::getHeaderActions(),

            ActionGroup::make([
                MoveAudioAction::make('move-audio'),

                DeleteAudioAction::make('delete-audio'),
            ]),
        ];
    }
}
