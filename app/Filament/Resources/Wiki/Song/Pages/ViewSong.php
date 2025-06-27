<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\Pages;

use App\Filament\HeaderActions\Models\Wiki\Song\AttachSongResourceHeaderAction;
use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Wiki\Song;
use Filament\Actions\ActionGroup;

/**
 * Class ViewSong.
 */
class ViewSong extends BaseViewResource
{
    protected static string $resource = Song::class;

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
                AttachSongResourceHeaderAction::make('attach-song-resource'),
            ]),
        ];
    }
}
