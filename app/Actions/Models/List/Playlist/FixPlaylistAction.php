<?php

declare(strict_types=1);

namespace App\Actions\Models\List\Playlist;

use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Class FixPlaylistAction.
 */
class FixPlaylistAction
{
    /**
     * Handle the action.
     *
     * @param  Playlist  $playlist,
     * @param  mixed  $context
     * @return int
     */
    public function handle(Playlist $playlist, mixed $context = null): int
    {
        // Fetch all tracks in the playlist and index them by track_id
        $messages[] = "Fetching tracks for playlist ID: {$playlist->getKey()}...";

        /** @var Collection<int, PlaylistTrack> $tracks */
        $tracks = PlaylistTrack::where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist->getKey())
            ->orderBy(PlaylistTrack::ATTRIBUTE_ID)
            ->get()
            ->keyBy(PlaylistTrack::ATTRIBUTE_ID);

        // Find the playlist and get the first_id
        $messages[] =
        $this->sendMessage("Fetching playlist details...", $context, 'info');

        $first_id = $playlist->first_id;

        // Initialize arrays for ordered tracks and visited tracks
        $orderedTracks = [];
        $visitedTracks = [];

        // Start at the first_id and follow the next_id chain
        $this->sendMessage("Reconstructing the playlist order...", $context, 'info');

        $current_id = $first_id;

        while ($current_id !== null) {
            if (isset($visitedTracks[$current_id])) {
                // If we encounter a previously visited track, we've detected a cycle
                $this->sendMessage("Cycle detected at track ID: $current_id, stopping...", $context, 'warn');
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
            $this->sendMessage("Detected broken tracks, attempting to add them to the end of the list...", $context, 'warn');

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
        $this->sendMessage("Updating track links (previous_id and next_id)...", $context, 'info');
        DB::transaction(function () use ($orderedTracks, $tracks, $playlist, $context) {
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

            $this->sendMessage("Playlist first_id and last_id updated successfully.", $context, 'info');
        });

        $this->sendMessage("Playlist fixed successfully.", $context, 'info');
        return 1;
    }

    /**
     * Send the message given the context.
     *
     * @param  string  $message
     * @param  mixed  $context
     * @param  string  $type
     * @return void
     */
    protected function sendMessage(string $message, mixed $context, string $type): void
    {
        if (is_null($context)) {
            return;
        }

        $translatedType = match (true) {
            $context === 'log' => match ($type) {
                'info' => 'info',
                'warn' => 'warning',
            },
            default => $type,
        };

        match (true) {
            $context instanceof Command => $context->$translatedType($message),
            $context === 'log' => Log::$translatedType($message),
        };
    }
}
