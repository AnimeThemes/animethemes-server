<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\SongStaff\Pages;

use App\Filament\Actions\Base\CreateAction;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Song\RelationManagers\SongStaffSongRelationManager;
use App\Filament\Resources\Wiki\SongStaffResource;
use App\Models\Wiki\Song;
use App\Models\Wiki\SongStaff;
use Illuminate\Support\Arr;

class ListSongStaff extends BaseListResources
{
    protected static string $resource = SongStaffResource::class;

    /**
     * Get the header actions available.
     *
     * @return \Filament\Actions\Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->action(function (array $data): void {
                    $staff = Arr::get($data, Song::RELATION_STAFF);
                    SongStaffSongRelationManager::saveArtists(Arr::get($data, SongStaff::ATTRIBUTE_SONG), $staff);
                }),
        ];
    }
}
