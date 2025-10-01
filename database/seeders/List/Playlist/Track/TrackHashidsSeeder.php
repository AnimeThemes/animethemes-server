<?php

declare(strict_types=1);

namespace Database\Seeders\List\Playlist\Track;

use App\Actions\Models\AssignHashidsAction;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class TrackHashidsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PlaylistTrack::query()
            ->chunkById(100, fn (Collection $tracks) => $tracks->each(function (PlaylistTrack $track): void {
                $action = new AssignHashidsAction();

                $action->assign($track, 'playlists');
            }));
    }
}
