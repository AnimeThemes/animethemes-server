<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\Pages;

use App\Filament\HeaderActions\Models\Wiki\Song\AttachSongResourceHeaderAction;
use App\Filament\Resources\Base\BaseEditResource;
use App\Filament\Resources\Wiki\Song;
use Filament\Actions\ActionGroup;

/**
 * Class EditSong.
 */
class EditSong extends BaseEditResource
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
        return array_merge(
            parent::getHeaderActions(),
            [
                ActionGroup::make([
                    AttachSongResourceHeaderAction::make('attach-song-resource'),
                ])
            ],
        );
    }
}
