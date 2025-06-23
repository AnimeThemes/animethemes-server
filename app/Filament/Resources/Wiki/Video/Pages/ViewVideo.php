<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video\Pages;

use App\Filament\Actions\Models\Wiki\Video\BackfillAudioAction;
use App\Filament\Actions\Storage\MoveAllAction;
use App\Filament\Actions\Storage\Wiki\Video\DeleteVideoAction;
use App\Filament\Actions\Storage\Wiki\Video\MoveVideoAction;
use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Wiki\Video;
use Filament\Actions\ActionGroup;

/**
 * Class ViewVideo.
 */
class ViewVideo extends BaseViewResource
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
        return [
            ...parent::getHeaderActions(),

            ActionGroup::make([
                BackfillAudioAction::make('backfill-audio'),

                MoveVideoAction::make('move-video'),

                MoveAllAction::make('move-all'),

                DeleteVideoAction::make('delete-video'),
            ]),
        ];
    }
}
