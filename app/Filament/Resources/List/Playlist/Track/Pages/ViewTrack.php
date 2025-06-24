<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\Playlist\Track\Pages;

use App\Filament\Actions\Models\List\AssignHashidsAction;
use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\List\Playlist\Track;
use App\Models\List\Playlist\PlaylistTrack;

/**
 * Class ViewTrack.
 */
class ViewTrack extends BaseViewResource
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
        return [
            ...parent::getHeaderActions(),

            AssignHashidsAction::make('assign-hashids')
                ->setConnection('playlists')
                ->authorize('update', PlaylistTrack::class),
        ];
    }
}
