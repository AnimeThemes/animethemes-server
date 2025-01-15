<?php

declare(strict_types=1);

namespace App\Console\Commands\Models;

use App\Actions\Models\List\Playlist\FixPlaylistAction;
use App\Console\Commands\BaseCommand;
use App\Models\List\Playlist;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Class PlaylistFixCommand.
 */
class PlaylistFixCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'playlist:fix {playlistId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix playlist tracks to remove cycles';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $playlistId = $this->argument('playlistId');

        /** @var Playlist|null $playlist */
        $playlist = Playlist::find($playlistId);

        if (!$playlist) {
            $this->error("Playlist with id $playlistId not found.");
            return 0;
        }

        return new FixPlaylistAction()->handle($playlist, $this);
    }

    /**
     * Get the validator for options.
     *
     * @return Validator
     */
    protected function validator(): Validator
    {
        return ValidatorFacade::make($this->options(), [
            'playlistId' => ['int', 'required'],
        ]);
    }
}
