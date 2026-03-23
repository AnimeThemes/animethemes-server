<?php

declare(strict_types=1);

namespace App\Pivots\Wiki;

use App\Events\Pivot\Wiki\ArtistMember\ArtistMemberCreated;
use App\Events\Pivot\Wiki\ArtistMember\ArtistMemberDeleted;
use App\Events\Pivot\Wiki\ArtistMember\ArtistMemberUpdated;
use App\Models\Wiki\Artist;
use App\Pivots\BasePivot;
use Database\Factories\Pivots\Wiki\ArtistMemberFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Artist $artist
 * @property int $artist_id
 * @property string|null $alias
 * @property string|null $as
 * @property Artist $member
 * @property int $member_id
 * @property string|null $notes
 * @property int $relevance
 *
 * @method static ArtistMemberFactory factory(...$parameters)
 */
#[Table(ArtistMember::TABLE)]
class ArtistMember extends BasePivot
{
    final public const string TABLE = 'artist_member';

    final public const string ATTRIBUTE_ALIAS = 'alias';
    final public const string ATTRIBUTE_AS = 'as';
    final public const string ATTRIBUTE_ARTIST = 'artist_id';
    final public const string ATTRIBUTE_MEMBER = 'member_id';
    final public const string ATTRIBUTE_NOTES = 'notes';
    final public const string ATTRIBUTE_RELEVANCE = 'relevance';

    final public const string RELATION_ARTIST = 'artist';
    final public const string RELATION_MEMBER = 'member';

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => ArtistMemberCreated::class,
        'deleted' => ArtistMemberDeleted::class,
        'updated' => ArtistMemberUpdated::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        ArtistMember::ATTRIBUTE_ALIAS,
        ArtistMember::ATTRIBUTE_ARTIST,
        ArtistMember::ATTRIBUTE_AS,
        ArtistMember::ATTRIBUTE_MEMBER,
        ArtistMember::ATTRIBUTE_NOTES,
        ArtistMember::ATTRIBUTE_RELEVANCE,
    ];

    /**
     * Get the composite primary key for the pivot.
     *
     * @return string[]
     */
    protected function getPrimaryKeys(): array
    {
        return [
            ArtistMember::ATTRIBUTE_ARTIST,
            ArtistMember::ATTRIBUTE_MEMBER,
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            ArtistMember::ATTRIBUTE_ALIAS => 'string',
            ArtistMember::ATTRIBUTE_ARTIST => 'int',
            ArtistMember::ATTRIBUTE_AS => 'string',
            ArtistMember::ATTRIBUTE_MEMBER => 'int',
            ArtistMember::ATTRIBUTE_NOTES => 'string',
            ArtistMember::ATTRIBUTE_RELEVANCE => 'int',
        ];
    }

    /**
     * Gets the artist that owns the artist member.
     *
     * @return BelongsTo<Artist, $this>
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class, ArtistMember::ATTRIBUTE_ARTIST);
    }

    /**
     * Gets the member that owns the artist member.
     *
     * @return BelongsTo<Artist, $this>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Artist::class, ArtistMember::ATTRIBUTE_MEMBER);
    }
}
