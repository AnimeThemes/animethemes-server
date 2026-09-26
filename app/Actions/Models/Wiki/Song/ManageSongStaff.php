<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Song;

use App\Models\Wiki\Song;
use App\Models\Wiki\SongStaff;
use App\Pivots\Wiki\ArtistMember;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManageSongStaff
{
    protected Song $song;

    public function __construct(
        Song|int $song,
        /** @var Collection<int, non-empty-array<string, mixed>> */
        protected Collection $staff = new Collection(),
        /** @var Collection<string, array<string, mixed>> */
        protected Collection $members = new Collection(),
    ) {
        $this->song = $song instanceof Song ? $song : Song::query()->find($song);
    }

    public function addArtist(
        string $role,
        int $artist,
        ?int $member = null,
        ?string $alias = null,
        ?string $as = null,
        ?string $memberAlias = null,
        ?string $memberAs = null
    ): static {
        $this->staff->push([
            SongStaff::ATTRIBUTE_SONG => $this->song->getKey(),
            SongStaff::ATTRIBUTE_ARTIST => $artist,
            SongStaff::ATTRIBUTE_MEMBER => $member,
            SongStaff::ATTRIBUTE_ALIAS => filled($alias) ? trim($alias) : null,
            SongStaff::ATTRIBUTE_AS => filled($as) ? trim($as) : null,
            SongStaff::ATTRIBUTE_ROLE => $role,
            SongStaff::ATTRIBUTE_MEMBER_ALIAS => filled($memberAlias) ? trim($memberAlias) : null,
            SongStaff::ATTRIBUTE_MEMBER_AS => filled($memberAs) ? trim($memberAs) : null,
        ]);

        if ($member !== null) {
            $this->members->put($artist.':'.$member, [
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

            $new = collect($this->staff)
                ->keyBy(fn (array $p): string => $p[SongStaff::ATTRIBUTE_ROLE].':'.$p[SongStaff::ATTRIBUTE_ARTIST].':'.($p[SongStaff::ATTRIBUTE_MEMBER] ?? ''));

            $existing = SongStaff::query()
                ->whereBelongsTo($this->song)
                ->get()
                ->keyBy(fn (SongStaff $s): string => $s->role.':'.$s->artist_id.':'.($s->member_id ?? ''));

            $models = $new->map(
                fn (array $staff) => SongStaff::query()->updateOrCreate(
                    Arr::only($staff, [SongStaff::ATTRIBUTE_SONG, SongStaff::ATTRIBUTE_ARTIST, SongStaff::ATTRIBUTE_MEMBER, SongStaff::ATTRIBUTE_ROLE]),
                    Arr::only($staff, [SongStaff::ATTRIBUTE_ALIAS, SongStaff::ATTRIBUTE_AS, SongStaff::ATTRIBUTE_MEMBER_ALIAS, SongStaff::ATTRIBUTE_MEMBER_AS])
                )
            );

            $existing->diffKeys($new)->each->delete();

            SongStaff::setNewOrder($models->pluck(SongStaff::ATTRIBUTE_ID)->all());

            $members = $this->members
                ->unique(
                    fn (array $member) => $member[ArtistMember::ATTRIBUTE_ARTIST].':'.$member[ArtistMember::ATTRIBUTE_MEMBER]
                )
                ->values();

            // Update artist_member table to match member performances
            ArtistMember::query()->upsert(
                $members->all(),
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
