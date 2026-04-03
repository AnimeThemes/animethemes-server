<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Song;

use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Performance;
use App\Pivots\Wiki\ArtistMember;
use Exception;
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
        protected Collection $members = new Collection(),
    ) {
        $this->song = $song instanceof Song ? $song : Song::query()->find($song);
    }

    public function addArtist(
        int $artist,
        ?int $member = null,
        ?string $alias = null,
        ?string $as = null,
        ?string $memberAlias = null,
        ?string $memberAs = null
    ): static {
        $this->performances->push([
            Performance::ATTRIBUTE_SONG => $this->song->getKey(),
            Performance::ATTRIBUTE_ARTIST => $artist,
            Performance::ATTRIBUTE_MEMBER => $member,
            Performance::ATTRIBUTE_ALIAS => filled($alias) ? trim($alias) : null,
            Performance::ATTRIBUTE_AS => filled($as) ? trim($as) : null,
            Performance::ATTRIBUTE_MEMBER_ALIAS => filled($memberAlias) ? trim($memberAlias) : null,
            Performance::ATTRIBUTE_MEMBER_AS => filled($memberAs) ? trim($memberAs) : null,
        ]);

        if ($member !== null) {
            $this->members->put($member, [
                ArtistMember::ATTRIBUTE_ARTIST => $artist,
                ArtistMember::ATTRIBUTE_MEMBER => $member,
                ArtistMember::ATTRIBUTE_ALIAS => filled($memberAlias) ? trim($memberAlias) : null,
                ArtistMember::ATTRIBUTE_AS => filled($memberAs) ? trim($memberAs) : null,
            ]);
        }

        return $this;
    }

    public function commit(): static
    {
        try {
            DB::beginTransaction();

            $new = collect($this->performances)
                ->keyBy(fn (array $p): string => $p[Performance::ATTRIBUTE_ARTIST].':'.($p[Performance::ATTRIBUTE_MEMBER] ?? ''));

            $existing = Performance::query()
                ->whereBelongsTo($this->song)
                ->get()
                ->keyBy(fn (Performance $p): string => $p->artist_id.':'.($p->member_id ?? ''));

            $models = $new->map(
                fn (array $performance) => Performance::query()->updateOrCreate(
                    Arr::only($performance, [Performance::ATTRIBUTE_SONG, Performance::ATTRIBUTE_ARTIST, Performance::ATTRIBUTE_MEMBER]),
                    Arr::only($performance, [Performance::ATTRIBUTE_ALIAS, Performance::ATTRIBUTE_AS, Performance::ATTRIBUTE_MEMBER_ALIAS, Performance::ATTRIBUTE_MEMBER_AS])
                )
            );

            $existing->diffKeys($new)->each->delete();

            Performance::setNewOrder($models->pluck(Performance::ATTRIBUTE_ID)->all());

            // Update artist_member table to match member performances
            ArtistMember::query()->upsert(
                $this->members->all(),
                [ArtistMember::ATTRIBUTE_ARTIST, ArtistMember::ATTRIBUTE_MEMBER],
                [ArtistMember::ATTRIBUTE_ALIAS, ArtistMember::ATTRIBUTE_AS],
            );

            static::setArtistMemberRelevance();

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
    public static function setArtistMemberRelevance(): void
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
}
