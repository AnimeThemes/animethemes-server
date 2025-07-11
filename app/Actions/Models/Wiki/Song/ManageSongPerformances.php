<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Song;

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use App\Pivots\Wiki\ArtistMember;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ManageSongPerformances.
 */
class ManageSongPerformances
{
    protected int $song;
    protected array $groups = [];
    protected array $performances = [];

    /**
     * Add the song of the performances.
     *
     * @param  Song|int  $song
     * @return static
     */
    public function forSong(Song|int $song): static
    {
        $this->song = $song instanceof Song ? $song->getKey() : $song;

        return $this;
    }

    /**
     * Add a single artist to the song performance.
     *
     * @param  int  $artist
     * @param  string|null  $alias
     * @param  string|null  $as
     * @return static
     */
    public function addSingleArtist(int $artist, ?string $alias = null, ?string $as = null): static
    {
        $this->performances[] = [
            Performance::ATTRIBUTE_ARTIST_TYPE => Artist::class,
            Performance::ATTRIBUTE_ARTIST_ID => $artist,
            Performance::ATTRIBUTE_ALIAS => filled($alias) ? trim($alias) : null,
            Performance::ATTRIBUTE_AS => filled($as) ? trim($as) : null,
        ];

        return $this;
    }

    /**
     * Add a group data to the performance.
     *
     * @param  int  $group
     * @param  string|null  $alias
     * @param  string|null  $as
     * @return static
     */
    public function addGroupData(int $group, ?string $alias = null, ?string $as = null): static
    {
        $this->groups[$group] = [
            Performance::ATTRIBUTE_ALIAS => filled($alias) ? trim($alias) : null,
            Performance::ATTRIBUTE_AS => filled($as) ? trim($as) : null,
        ];

        return $this;
    }

    /**
     * Add a membership to the song.
     *
     * @param  int  $group
     * @param  int  $member
     * @param  string|null  $alias
     * @param  string|null  $as
     * @return static
     */
    public function addMembership(int $group, int $member, ?string $alias = null, ?string $as = null): static
    {
        $this->performances[] = [
            Performance::ATTRIBUTE_ARTIST_TYPE => Membership::class,
            Performance::ATTRIBUTE_ALIAS => Arr::get($this->groups, "{$group}.".Performance::ATTRIBUTE_ALIAS),
            Performance::ATTRIBUTE_AS => Arr::get($this->groups, "{$group}.".Performance::ATTRIBUTE_AS),
            Performance::RELATION_MEMBERSHIP => [
                Membership::ATTRIBUTE_ARTIST => $group,
                Membership::ATTRIBUTE_MEMBER => $member,
                Membership::ATTRIBUTE_ALIAS => filled($alias) ? trim($alias) : null,
                Membership::ATTRIBUTE_AS => filled($as) ? trim($as) : null,
            ],
        ];

        return $this;
    }

    /**
     * Commit the performances to the song.
     *
     * @return static
     */
    public function commit(): static
    {
        try {
            DB::beginTransaction();

            $performancesToCreate = [];
            $memberships = [];

            foreach ($this->performances as $performance) {
                $membershipData = Arr::get($performance, Performance::RELATION_MEMBERSHIP);

                $data = [
                    ...$performance,
                    Performance::ATTRIBUTE_SONG => $this->song,
                ];

                if ($membershipData) {
                    $membership = Membership::query()->firstOrCreate($membershipData);
                    $memberships[] = $membershipData;

                    $data = [
                        ...Arr::except($performance, Performance::RELATION_MEMBERSHIP),
                        Performance::ATTRIBUTE_SONG => $this->song,
                        Performance::ATTRIBUTE_ARTIST_TYPE => Membership::class,
                        Performance::ATTRIBUTE_ARTIST_ID => $membership->getKey(),
                    ];
                }

                $performancesToCreate[] = $data;
            }

            // Multiple queries because upsert does not dispatch an event.
            foreach ($performancesToCreate as $performance) {
                $model = Performance::query()->updateOrCreate(
                    Arr::only($performance, [Performance::ATTRIBUTE_SONG, Performance::ATTRIBUTE_ARTIST_TYPE, Performance::ATTRIBUTE_ARTIST_ID]),
                    [
                        Performance::ATTRIBUTE_ALIAS => Arr::get($performance, Performance::ATTRIBUTE_ALIAS),
                        Performance::ATTRIBUTE_AS => Arr::get($performance, Performance::ATTRIBUTE_AS),
                    ]
                );
                // Note: Working in prod. Saving this here for local tests. Same for deleted events.
                // $event = $model->wasRecentlyCreated ? new PerformanceCreated($model) : new PerformanceUpdated($model);
                // $event->syncArtistSong();
            }

            // Delete membership performances that are not in the new list.
            $membershipPerformances = Performance::query()
                ->where(Performance::ATTRIBUTE_SONG, $this->song)
                ->where(Performance::ATTRIBUTE_ARTIST_TYPE, Membership::class)
                ->whereNotIn(
                    Performance::ATTRIBUTE_ARTIST_ID,
                    Arr::map(
                        Arr::where($performancesToCreate, fn ($performance) => $performance[Performance::ATTRIBUTE_ARTIST_TYPE] === Membership::class),
                        fn ($performanceMembership) => Arr::get($performanceMembership, Performance::ATTRIBUTE_ARTIST_ID)
                    )
                );

            foreach ($membershipPerformances->get() as $performance) {
                $performance->forceDelete();
                // new PerformanceDeleted($performance)->syncArtistSong();
            }

            // Delete solo performances that are not in the new list.
            $soloPerformances = Performance::query()
                ->where(Performance::ATTRIBUTE_SONG, $this->song)
                ->where(Performance::ATTRIBUTE_ARTIST_TYPE, Artist::class)
                ->whereNotIn(
                    Performance::ATTRIBUTE_ARTIST_ID,
                    Arr::map(
                        Arr::where($performancesToCreate, fn ($performance) => $performance[Performance::ATTRIBUTE_ARTIST_TYPE] === Artist::class),
                        fn ($solo) => Arr::get($solo, Performance::ATTRIBUTE_ARTIST_ID)
                    )
                );

            foreach ($soloPerformances->get() as $performance) {
                $performance->forceDelete();
                // new PerformanceDeleted($performance)->syncArtistSong();
            }

            // Update artist_member table to match memberships
            ArtistMember::query()->upsert(
                $memberships,
                [ArtistMember::ATTRIBUTE_ARTIST, ArtistMember::ATTRIBUTE_MEMBER],
                [ArtistMember::ATTRIBUTE_ALIAS, ArtistMember::ATTRIBUTE_AS],
            );

            DB::commit();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }

        return $this;
    }
}
