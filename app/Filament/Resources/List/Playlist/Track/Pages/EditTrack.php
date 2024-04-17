<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\Playlist\Track\Pages;

use App\Filament\HeaderActions\Models\AssignHashidsHeaderAction;
use App\Filament\Resources\List\Playlist\Track;
use App\Filament\Resources\Base\BaseEditResource;
use App\Models\List\Playlist\PlaylistTrack;

/**
 * Class EditTrack.
 */
class EditTrack extends BaseEditResource
{
    protected static string $resource = Track::class;

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
                    ->label(__('filament.actions.models.assign_hashids.name'))
                    ->setConnection('playlists')
                    ->requiresConfirmation()
                    ->authorize('update', PlaylistTrack::class)
            ],
        );
    }
}
