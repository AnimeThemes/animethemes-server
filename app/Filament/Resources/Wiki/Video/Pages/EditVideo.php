<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video\Pages;

use App\Filament\HeaderActions\Models\Wiki\Video\BackfillAudioHeaderAction;
use App\Filament\HeaderActions\Storage\MoveAllHeaderAction;
use App\Filament\HeaderActions\Storage\Wiki\Video\DeleteVideoHeaderAction;
use App\Filament\HeaderActions\Storage\Wiki\Video\MoveVideoHeaderAction;
use App\Filament\Resources\Wiki\Video;
use App\Filament\Resources\Base\BaseEditResource;
use Filament\Actions\ActionGroup;

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
                    BackfillAudioHeaderAction::make('backfill-audio'),

                    MoveVideoHeaderAction::make('move-video'),

                    MoveAllHeaderAction::make('move-all'),

                    DeleteVideoHeaderAction::make('delete-video'),
                ]),
            ],
        );
    }
}
