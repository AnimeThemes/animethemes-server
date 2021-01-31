<?php

namespace App\Models;

use App\Contracts\Nameable;
use App\Events\Artist\ArtistCreated;
use App\Events\Artist\ArtistDeleted;
use App\Events\Artist\ArtistUpdated;
use App\Pivots\ArtistImage;
use App\Pivots\ArtistMember;
use App\Pivots\ArtistResource;
use App\Pivots\ArtistSong;
use ElasticScoutDriverPlus\CustomSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Contracts\Auditable;

class Artist extends Model implements Auditable, Nameable
{
    use CustomSearch, HasFactory, Searchable;
    use \OwenIt\Auditing\Auditable;

    /**
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
    public function toSearchableArray()
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
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the songs the artist has performed in.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function songs()
    {
        return $this->belongsToMany('App\Models\Song', 'artist_song', 'artist_id', 'song_id')->using(ArtistSong::class)->withPivot('as');
    }

    /**
     * Get the resources for the artist.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function externalResources()
    {
        return $this->belongsToMany('App\Models\ExternalResource', 'artist_resource', 'artist_id', 'resource_id')->using(ArtistResource::class)->withPivot('as');
    }

    /**
     * Get the members that comprise this group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany('App\Models\Artist', 'artist_member', 'artist_id', 'member_id')->using(ArtistMember::class)->withPivot('as');
    }

    /**
     * Get the groups the artist has performed in.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany('App\Models\Artist', 'artist_member', 'member_id', 'artist_id')->using(ArtistMember::class)->withPivot('as');
    }

    /**
     * Get the images for the artist.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function images()
    {
        return $this->belongsToMany('App\Models\Image', 'artist_image', 'artist_id', 'image_id')->using(ArtistImage::class);
    }
}
