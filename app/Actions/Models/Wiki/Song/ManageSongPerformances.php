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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManageSongPerformances
{
    protected Song $song;

    /** @var Collection<int, array{alias: string|null, as: string|null}> */
    protected Collection $groups;

    /** @var Collection<int, non-empty-array<string, mixed>> */
    protected Collection $performances;

    /** @var Collection<int, array<string, mixed>> */
    protected Collection $memberships;

    public function __construct()
    {
        $this->groups = new Collection();
        $this->performances = new Collection();
        $this->memberships = new Collection();
    }

    public function forSong(Song|int $song): static
    {
        $this->song = $song instanceof Song ? $song : Song::query()->find($song);

        return $this;
    }

    public function addSingleArtist(int $artist, ?string $alias = null, ?string $as = null): static
    {
        $this->performances->push([
            Performance::ATTRIBUTE_ARTIST_TYPE => Relation::getMorphAlias(Artist::class),
            Performance::ATTRIBUTE_ARTIST_ID => $artist,
            Performance::ATTRIBUTE_ALIAS => filled($alias) ? trim($alias) : null,
            Performance::ATTRIBUTE_AS => filled($as) ? trim($as) : null,
        ]);

        return $this;
    }

    public function addGroupData(int $group, ?string $alias = null, ?string $as = null): static
    {
        $this->groups->put($group, [
            Performance::ATTRIBUTE_ALIAS => filled($alias) ? trim($alias) : null,
            Performance::ATTRIBUTE_AS => filled($as) ? trim($as) : null,
        ]);

        return $this;
    }

    public function addMembership(int $group, int $member, ?string $alias = null, ?string $as = null): static
    {
        $membership = Membership::query()->firstOrCreate([
            Membership::ATTRIBUTE_ARTIST => $group,
            Membership::ATTRIBUTE_MEMBER => $member,
            Membership::ATTRIBUTE_ALIAS => filled($alias) ? trim($alias) : null,
            Membership::ATTRIBUTE_AS => filled($as) ? trim($as) : null,
        ]);

        $this->memberships->put(
            $membership->getKey(),
            Arr::only(
                $membership->attributesToArray(),
                [Membership::ATTRIBUTE_ARTIST, Membership::ATTRIBUTE_MEMBER, Membership::ATTRIBUTE_ALIAS, Membership::ATTRIBUTE_AS]
            )
        );

        $this->performances->push([
            Performance::ATTRIBUTE_ARTIST_TYPE => $membership->getMorphClass(),
            Performance::ATTRIBUTE_ARTIST_ID => $membership->getKey(),
            Performance::ATTRIBUTE_ALIAS => Arr::get($this->groups->get($group), Performance::ATTRIBUTE_ALIAS),
            Performance::ATTRIBUTE_AS => Arr::get($this->groups->get($group), Performance::ATTRIBUTE_AS),
        ]);

        return $this;
    }

    public function commit(): static
    {
        try {
            DB::beginTransaction();

            $this->performances = $this->performances->map(
                fn (array $performance): array => [
                    ...$performance,
                    Performance::ATTRIBUTE_SONG => $this->song->getKey(),
                ]
            );

            // Multiple queries because upsert does not dispatch an event.
            foreach ($this->performances->all() as $performance) {
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
                ->whereBelongsTo($this->song)
                ->where(Performance::ATTRIBUTE_ARTIST_TYPE, Relation::getMorphAlias(Membership::class))
                ->whereNotIn(
                    Performance::ATTRIBUTE_ARTIST_ID,
                    $this->performances->filter(fn (array $performance): bool => $performance[Performance::ATTRIBUTE_ARTIST_TYPE] === Relation::getMorphAlias(Membership::class))
                        ->map(fn (array $performanceMembership) => Arr::get($performanceMembership, Performance::ATTRIBUTE_ARTIST_ID))
                        ->all(),
                );

            $membershipPerformances->each(fn (Performance $performance) => $performance->delete());

            // Delete solo performances that are not in the new list.
            $soloPerformances = Performance::query()
                ->whereBelongsTo($this->song)
                ->where(Performance::ATTRIBUTE_ARTIST_TYPE, Relation::getMorphAlias(Artist::class))
                ->whereNotIn(
                    Performance::ATTRIBUTE_ARTIST_ID,
                    $this->performances->filter(fn (array $performance): bool => $performance[Performance::ATTRIBUTE_ARTIST_TYPE] === Relation::getMorphAlias(Artist::class))
                        ->map(fn (array $solo) => Arr::get($solo, Performance::ATTRIBUTE_ARTIST_ID))
                        ->all(),
                );

            $soloPerformances->each(fn (Performance $performance) => $performance->delete());

            Performance::withoutEvents(function (): void {
                foreach ($this->performances->all() as $index => $performance) {
                    Performance::query()
                        ->whereBelongsTo($this->song)
                        ->where(Performance::ATTRIBUTE_ARTIST_TYPE, Arr::get($performance, Performance::ATTRIBUTE_ARTIST_TYPE))
                        ->where(Performance::ATTRIBUTE_ARTIST_ID, Arr::get($performance, Performance::ATTRIBUTE_ARTIST_ID))
                        ->update([Performance::ATTRIBUTE_RELEVANCE => $index + 1]);
                }
            });

            // Update artist_member table to match memberships
            ArtistMember::query()->upsert(
                $this->memberships->all(),
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
        $songArtists = [];
        foreach ($this->performances->all() as $performance) {
            if ($performance[Performance::ATTRIBUTE_ARTIST_TYPE] === Relation::getMorphAlias(Membership::class)) {
                $groupOrArtist = Arr::get(
                    $this->memberships->get(Arr::get($performance, Performance::ATTRIBUTE_ARTIST_ID)),
                    Membership::ATTRIBUTE_ARTIST
                );
            } else {
                $groupOrArtist = Arr::get($performance, Performance::ATTRIBUTE_ARTIST_ID);
            }

            $songArtists[$groupOrArtist] = [
                ArtistSong::ATTRIBUTE_ALIAS => Arr::get($performance, Performance::ATTRIBUTE_ALIAS),
                ArtistSong::ATTRIBUTE_AS => Arr::get($performance, Performance::ATTRIBUTE_AS),
            ];
        }

        ArtistSong::withoutEvents(function () use ($songArtists): void {
            $this->song->artists()->sync($songArtists);
        });
    }
}
