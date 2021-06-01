<?php

declare(strict_types=1);

namespace App\Models;

use App\Events\Artist\ArtistCreated;
use App\Events\Artist\ArtistDeleted;
use App\Events\Artist\ArtistRestored;
use App\Events\Artist\ArtistUpdated;
use App\Pivots\ArtistImage;
use App\Pivots\ArtistMember;
use App\Pivots\ArtistResource;
use App\Pivots\ArtistSong;
use ElasticScoutDriverPlus\QueryDsl;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

/**
 * Class Artist
 * @package App\Models
 */
class Artist extends BaseModel
{
    use QueryDsl;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
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
    protected $table = 'artist';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'artist_id';

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
        return $this->belongsToMany('App\Models\Song', 'artist_song', 'artist_id', 'song_id')
            ->using(ArtistSong::class)
            ->withPivot('as')
            ->withTimestamps();
    }

    /**
     * Get the resources for the artist.
     *
     * @return BelongsToMany
     */
    public function externalResources(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\ExternalResource', 'artist_resource', 'artist_id', 'resource_id')
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
        return $this->belongsToMany('App\Models\Artist', 'artist_member', 'artist_id', 'member_id')
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
        return $this->belongsToMany('App\Models\Artist', 'artist_member', 'member_id', 'artist_id')
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
        return $this->belongsToMany('App\Models\Image', 'artist_image', 'artist_id', 'image_id')
            ->using(ArtistImage::class)
            ->withTimestamps();
    }
}
