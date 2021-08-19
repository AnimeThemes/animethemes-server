<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Events\Wiki\Artist\ArtistCreated;
use App\Events\Wiki\Artist\ArtistDeleted;
use App\Events\Wiki\Artist\ArtistRestored;
use App\Events\Wiki\Artist\ArtistUpdated;
use App\Models\BaseModel;
use App\Pivots\ArtistImage;
use App\Pivots\ArtistMember;
use App\Pivots\ArtistResource;
use App\Pivots\ArtistSong;
use App\Pivots\BasePivot;
use Database\Factories\Wiki\ArtistFactory;
use ElasticScoutDriverPlus\QueryDsl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Laravel\Scout\Searchable;

/**
 * Class Artist.
 *
 * @property int $artist_id
 * @property string $slug
 * @property string $name
 * @property Collection $songs
 * @property Collection $resources
 * @property Collection $members
 * @property Collection $groups
 * @property Collection $images
 * @property BasePivot $pivot
 * @method static ArtistFactory factory(...$parameters)
 */
class Artist extends BaseModel
{
    use QueryDsl;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['slug', 'name'];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => ArtistCreated::class,
        'deleted' => ArtistDeleted::class,
        'restored' => ArtistRestored::class,
        'updated' => ArtistUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'artists';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'artist_id';

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with('songs');
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();
        $array['songs'] = $this->songs->toArray();

        return $array;
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the songs the artist has performed in.
     *
     * @return BelongsToMany
     */
    public function songs(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Wiki\Song', 'artist_song', 'artist_id', 'song_id')
            ->using(ArtistSong::class)
            ->withPivot('as')
            ->withTimestamps();
    }

    /**
     * Get the resources for the artist.
     *
     * @return BelongsToMany
     */
    public function resources(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Wiki\ExternalResource', 'artist_resource', 'artist_id', 'resource_id')
            ->using(ArtistResource::class)
            ->withPivot('as')
            ->withTimestamps();
    }

    /**
     * Get the members that comprise this group.
     *
     * @return BelongsToMany
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Wiki\Artist', 'artist_member', 'artist_id', 'member_id')
            ->using(ArtistMember::class)
            ->withPivot('as')
            ->withTimestamps();
    }

    /**
     * Get the groups the artist has performed in.
     *
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Wiki\Artist', 'artist_member', 'member_id', 'artist_id')
            ->using(ArtistMember::class)
            ->withPivot('as')
            ->withTimestamps();
    }

    /**
     * Get the images for the artist.
     *
     * @return BelongsToMany
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Wiki\Image', 'artist_image', 'artist_id', 'image_id')
            ->using(ArtistImage::class)
            ->withTimestamps();
    }
}
