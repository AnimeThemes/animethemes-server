<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\Playlist\Pages;

use App\Filament\HeaderActions\Models\AssignHashidsHeaderAction;
use App\Filament\Resources\List\Playlist;
use App\Filament\Resources\Base\BaseEditResource;
use App\Models\List\Playlist as PlaylistModel;

/**
 * Class EditPlaylist.
 */
class EditPlaylist extends BaseEditResource
{
    protected static string $resource = Playlist::class;

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
                AssignHashidsHeaderAction::make('assign-hashids')
                    ->setConnection('playlists')
                    ->authorize('update', PlaylistModel::class)
            ],
        );
    }
}
