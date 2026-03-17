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

    public function __construct(
        Song|int $song,
        /** @var Collection<int, non-empty-array<string, mixed>> */
        protected Collection $performances = new Collection(),
        /** @var Collection<int, array<string, mixed>> */
        protected Collection $memberships = new Collection(),
    ) {
        $this->song = $song instanceof Song ? $song : Song::query()->find($song);
    }

    public function addSingleArtist(int $artist, ?string $alias = null, ?string $as = null): static
    {
        $this->performances->push([
            Performance::ATTRIBUTE_SONG => $this->song->getKey(),
            Performance::ATTRIBUTE_ARTIST_TYPE => Relation::getMorphAlias(Artist::class),
            Performance::ATTRIBUTE_ARTIST_ID => $artist,
            Performance::ATTRIBUTE_ALIAS => filled($alias) ? trim($alias) : null,
            Performance::ATTRIBUTE_AS => filled($as) ? trim($as) : null,
        ]);

        return $this;
    }

    public function addMembership(
        int $group,
        int $member,
        ?string $alias = null,
        ?string $as = null,
        ?string $groupAlias = null,
        ?string $groupAs = null
    ): static {
        $membership = Membership::query()->firstOrCreate($attributes = [
            Membership::ATTRIBUTE_ARTIST => $group,
            Membership::ATTRIBUTE_MEMBER => $member,
            Membership::ATTRIBUTE_ALIAS => filled($alias) ? trim($alias) : null,
            Membership::ATTRIBUTE_AS => filled($as) ? trim($as) : null,
        ]);

        $this->memberships->put($membership->getKey(), $attributes);

        $this->performances->push([
            Performance::ATTRIBUTE_SONG => $this->song->getKey(),
            Performance::ATTRIBUTE_ARTIST_TYPE => $membership->getMorphClass(),
            Performance::ATTRIBUTE_ARTIST_ID => $membership->getKey(),
            Performance::ATTRIBUTE_ALIAS => filled($groupAlias) ? trim($groupAlias) : null,
            Performance::ATTRIBUTE_AS => filled($groupAs) ? trim($groupAs) : null,
        ]);

        return $this;
    }

    public function commit(): static
    {
        try {
            DB::beginTransaction();

            $new = collect($this->performances)
                ->keyBy(fn (array $p): string => $p[Performance::ATTRIBUTE_ARTIST_TYPE].':'.$p[Performance::ATTRIBUTE_ARTIST_ID]);

            $existing = Performance::query()
                ->whereBelongsTo($this->song)
                ->get()
                ->keyBy(fn (Performance $p): string => $p->artist_type.':'.$p->artist_id);

            $models = $new->map(
                fn (array $performance) => Performance::query()->updateOrCreate(
                    Arr::only($performance, [Performance::ATTRIBUTE_SONG, Performance::ATTRIBUTE_ARTIST_TYPE, Performance::ATTRIBUTE_ARTIST_ID]),
                    Arr::only($performance, [Performance::ATTRIBUTE_ALIAS, Performance::ATTRIBUTE_AS])
                )
            );

            $existing->diffKeys($new)->each->delete();

            Performance::setNewOrder($models->pluck(Performance::ATTRIBUTE_ID)->all());

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
        $songArtists = $this->performances->mapWithKeys(function (array $performance): array {
            $artistId = $performance[Performance::ATTRIBUTE_ARTIST_TYPE] === Relation::getMorphAlias(Membership::class)
                ? Arr::get($this->memberships->get($performance[Performance::ATTRIBUTE_ARTIST_ID]), Membership::ATTRIBUTE_ARTIST)
                : $performance[Performance::ATTRIBUTE_ARTIST_ID];

            return [
                $artistId => Arr::only($performance, [Performance::ATTRIBUTE_ALIAS, Performance::ATTRIBUTE_AS]),
            ];
        });

        ArtistSong::withoutEvents(fn () => $this->song->artists()->sync($songArtists));
    }
}
