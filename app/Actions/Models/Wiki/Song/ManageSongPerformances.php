<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Song;

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use App\Pivots\Wiki\ArtistMember;
use App\Pivots\Wiki\ArtistSong;
use Exception;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManageSongPerformances
{
    protected int $song;
    protected array $groups = [];
    protected array $performances = [];

    public function forSong(Song|int $song): static
    {
        $this->song = $song instanceof Song ? $song->getKey() : $song;

        return $this;
    }

    public function addSingleArtist(int $artist, ?string $alias = null, ?string $as = null): static
    {
        $this->performances[] = [
            Performance::ATTRIBUTE_ARTIST_TYPE => Relation::getMorphAlias(Artist::class),
            Performance::ATTRIBUTE_ARTIST_ID => $artist,
            Performance::ATTRIBUTE_ALIAS => filled($alias) ? trim($alias) : null,
            Performance::ATTRIBUTE_AS => filled($as) ? trim($as) : null,
        ];

        return $this;
    }

    public function addGroupData(int $group, ?string $alias = null, ?string $as = null): static
    {
        $this->groups[$group] = [
            Performance::ATTRIBUTE_ALIAS => filled($alias) ? trim($alias) : null,
            Performance::ATTRIBUTE_AS => filled($as) ? trim($as) : null,
        ];

        return $this;
    }

    public function addMembership(int $group, int $member, ?string $alias = null, ?string $as = null): static
    {
        $this->performances[] = [
            Performance::ATTRIBUTE_ARTIST_TYPE => Relation::getMorphAlias(Membership::class),
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
                        Performance::ATTRIBUTE_ARTIST_TYPE => Relation::getMorphAlias(Membership::class),
                        Performance::ATTRIBUTE_ARTIST_ID => $membership->getKey(),
                    ];
                }

                $performancesToCreate[] = $data;
            }

            // Multiple queries because upsert does not dispatch an event.
            foreach ($performancesToCreate as $performance) {
                Performance::query()->updateOrCreate(
                    Arr::only($performance, [Performance::ATTRIBUTE_SONG, Performance::ATTRIBUTE_ARTIST_TYPE, Performance::ATTRIBUTE_ARTIST_ID]),
                    [
                        Performance::ATTRIBUTE_ALIAS => Arr::get($performance, Performance::ATTRIBUTE_ALIAS),
                        Performance::ATTRIBUTE_AS => Arr::get($performance, Performance::ATTRIBUTE_AS),
                    ]
                );
            }

            // Delete membership performances that are not in the new list.
            $membershipPerformances = Performance::query()
                ->where(Performance::ATTRIBUTE_SONG, $this->song)
                ->where(Performance::ATTRIBUTE_ARTIST_TYPE, Relation::getMorphAlias(Membership::class))
                ->whereNotIn(
                    Performance::ATTRIBUTE_ARTIST_ID,
                    Arr::map(
                        Arr::where($performancesToCreate, fn ($performance): bool => $performance[Performance::ATTRIBUTE_ARTIST_TYPE] === Relation::getMorphAlias(Membership::class)),
                        fn ($performanceMembership) => Arr::get($performanceMembership, Performance::ATTRIBUTE_ARTIST_ID)
                    )
                );

            $membershipPerformances->each(fn (Performance $performance) => $performance->forceDelete());

            // Delete solo performances that are not in the new list.
            $soloPerformances = Performance::query()
                ->where(Performance::ATTRIBUTE_SONG, $this->song)
                ->where(Performance::ATTRIBUTE_ARTIST_TYPE, Relation::getMorphAlias(Artist::class))
                ->whereNotIn(
                    Performance::ATTRIBUTE_ARTIST_ID,
                    Arr::map(
                        Arr::where($performancesToCreate, fn ($performance): bool => $performance[Performance::ATTRIBUTE_ARTIST_TYPE] === Relation::getMorphAlias(Artist::class)),
                        fn ($solo) => Arr::get($solo, Performance::ATTRIBUTE_ARTIST_ID)
                    )
                );

            $soloPerformances->each(fn (Performance $performance) => $performance->forceDelete());

            Performance::withoutEvents(function () use ($performancesToCreate): void {
                foreach ($performancesToCreate as $index => $performanceToSort) {
                    Performance::query()
                        ->where(Performance::ATTRIBUTE_SONG, $this->song)
                        ->where(Performance::ATTRIBUTE_ARTIST_TYPE, Arr::get($performanceToSort, Performance::ATTRIBUTE_ARTIST_TYPE))
                        ->where(Performance::ATTRIBUTE_ARTIST_ID, Arr::get($performanceToSort, Performance::ATTRIBUTE_ARTIST_ID))
                        ->update([Performance::ATTRIBUTE_RELEVANCE => $index + 1]);
                }
            });

            // Update artist_member table to match memberships
            ArtistMember::query()->upsert(
                $memberships,
                [ArtistMember::ATTRIBUTE_ARTIST, ArtistMember::ATTRIBUTE_MEMBER],
                [ArtistMember::ATTRIBUTE_ALIAS, ArtistMember::ATTRIBUTE_AS],
            );

            $this->setArtistMemberRelevance();

            $this->syncSongArtist();

            DB::commit();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }

        return $this;
    }

    /**
     * Complex query to set relevance for artist members based on created_at order.
     */
    protected function setArtistMemberRelevance(): void
    {
        DB::statement('
            WITH base AS (
                SELECT
                    artist_id,
                    COALESCE(MAX(relevance), 0) AS max_rel
                FROM artist_member
                GROUP BY artist_id
            ),
            ranked AS (
                SELECT
                    artist_id,
                    member_id,
                    ROW_NUMBER() OVER (
                        PARTITION BY artist_id
                        ORDER BY created_at
                    ) AS rn
                FROM artist_member
                WHERE relevance IS NULL
            )
            UPDATE artist_member am
            JOIN ranked r
                ON am.artist_id = r.artist_id
            AND am.member_id = r.member_id
            JOIN base b
                ON b.artist_id = r.artist_id
            SET am.relevance = r.rn + b.max_rel;
        ');
    }

    /**
     * Temporary function where the performances feature synchronizes with the artist_song pivot table.
     */
    protected function syncSongArtist(): void
    {
        /** @var Song $song */
        $song = Song::query()
            ->whereKey($this->song)
            ->first();

        $songArtists = [];
        foreach ($this->performances as $performance) {
            $groupOrArtist = Arr::get($performance, Performance::RELATION_MEMBERSHIP.'.'.Membership::ATTRIBUTE_ARTIST)
                ?? Arr::get($performance, Performance::ATTRIBUTE_ARTIST_ID);

            $songArtists[$groupOrArtist] = [
                ArtistSong::ATTRIBUTE_ALIAS => Arr::get($performance, Performance::ATTRIBUTE_ALIAS),
                ArtistSong::ATTRIBUTE_AS => Arr::get($performance, Performance::ATTRIBUTE_AS),
            ];
        }

        ArtistSong::withoutEvents(function () use ($song, $songArtists): void {
            $song->artists()->sync($songArtists);
        });
    }
}
