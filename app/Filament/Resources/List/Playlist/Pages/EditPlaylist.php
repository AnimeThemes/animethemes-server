<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\Playlist\Pages;

use App\Filament\HeaderActions\Models\List\AssignHashidsHeaderAction;
use App\Filament\HeaderActions\Models\List\FixPlaylistHeaderAction;
use App\Filament\Resources\List\Playlist;
use App\Filament\Resources\Base\BaseEditResource;
use App\Models\List\Playlist as PlaylistModel;
use Filament\Actions\ActionGroup;

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
        return [
            ...parent::getHeaderActions(),

            ActionGroup::make([
                AssignHashidsHeaderAction::make('assign-hashids')
                    ->setConnection('playlists')
                    ->authorize('update', PlaylistModel::class),

                FixPlaylistHeaderAction::make('fix-playlist'),
            ]),
        ];
    }
}
