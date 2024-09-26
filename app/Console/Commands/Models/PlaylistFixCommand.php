<?php

declare(strict_types=1);

namespace App\Console\Commands\Models;

use App\Console\Commands\BaseCommand;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
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

        // Fetch all tracks in the playlist and index them by track_id
        $this->info("Fetching tracks for playlist ID: $playlistId...");

        /** @var Collection<PlaylistTrack> $tracks */
        $tracks = PlaylistTrack::where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlistId)
            ->orderBy(PlaylistTrack::ATTRIBUTE_ID)
            ->get()
            ->keyBy(PlaylistTrack::ATTRIBUTE_ID);

        // Find the playlist and get the first_id
        $this->info("Fetching playlist details...");

        $first_id = $playlist->first_id;

        // Initialize arrays for ordered tracks and visited tracks
        $orderedTracks = [];
        $visitedTracks = [];

        // Start at the first_id and follow the next_id chain
        $this->info("Reconstructing the playlist order...");

        $current_id = $first_id;

        while ($current_id !== null) {
            if (isset($visitedTracks[$current_id])) {
                // If we encounter a previously visited track, we've detected a cycle
                $this->warn("Cycle detected at track ID: $current_id, stopping...");
                break;
            }

            // Mark the current track as visited and add to ordered tracks
            $visitedTracks[$current_id] = true;
            $orderedTracks[] = $current_id;

            // Get the next track in sequence
            $currentTrack = $tracks[$current_id];
            $current_id = $currentTrack->next_id;
        }

        // Check if we have traversed all tracks; if not, some tracks are broken
        if (count($orderedTracks) != count($tracks)) {
            $this->warn("Detected broken tracks, attempting to add them to the end of the list...");

            // Find all tracks that were not visited (potentially broken)
            $remainingTracks = $tracks->filter(function ($track) use ($visitedTracks) {
                return !isset($visitedTracks[$track->track_id]);
            });

            // Add remaining tracks to the end, preserving the existing order as much as possible
            foreach ($remainingTracks as $remainingTrack) {
                $orderedTracks[] = $remainingTrack->track_id;
            }
        }

        // Update the previous_id and next_id for all tracks in the correct order
        $this->info("Updating track links (previous_id and next_id)...");
        DB::transaction(function () use ($orderedTracks, $tracks, $playlist) {
            $previous_id = null;

            foreach ($orderedTracks as $index => $track_id) {
                $track = $tracks[$track_id];

                // Get the next track in the sequence or set null if it's the last one
                $next_id = $orderedTracks[$index + 1] ?? null;

                // Update the previous_id and next_id for the track
                $track->previous_id = $previous_id;
                $track->next_id = $next_id;
                $track->save();

                // Move the previous_id pointer forward
                $previous_id = $track_id;
            }

            // Update the playlist's first_id and last_id
            $playlist->first_id = $orderedTracks[0];
            $playlist->last_id = $orderedTracks[count($orderedTracks) - 1];
            $playlist->save();

            $this->info("Playlist first_id and last_id updated successfully.");
        });

        $this->info("Playlist fixed successfully.");
        return 1;
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
