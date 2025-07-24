<?php

declare(strict_types=1);

namespace Database\Seeders\List\Playlist;

use App\Actions\Models\AssignHashidsAction;
use App\Models\List\Playlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class PlaylistHashidsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Playlist::query()
            ->chunkById(100, fn (Collection $playlists) => $playlists->each(function (Playlist $playlist) {
                $action = new AssignHashidsAction();

                $action->assign($playlist, 'playlists');
            }));
    }
}
