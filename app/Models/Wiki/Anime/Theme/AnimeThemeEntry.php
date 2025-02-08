<?php

declare(strict_types=1);

namespace App\Models\Wiki\Anime\Theme;

use App\Concerns\Models\Reportable;
use App\Contracts\Http\Api\InteractsWithSchema;
use App\Enums\Models\Wiki\ThemeType;
use App\Events\Wiki\Anime\Theme\Entry\EntryCreated;
use App\Events\Wiki\Anime\Theme\Entry\EntryDeleted;
use App\Events\Wiki\Anime\Theme\Entry\EntryDeleting;
use App\Events\Wiki\Anime\Theme\Entry\EntryRestored;
use App\Events\Wiki\Anime\Theme\Entry\EntryUpdated;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeThemeEntryVideoResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use App\Scopes\WithoutInsertSongScope;
use Database\Factories\Wiki\Anime\Theme\AnimeThemeEntryFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Znck\Eloquent\Relations\BelongsToThrough;

/**
 * Class AnimeThemeEntry.
 *
 * @property Anime $anime
 * @property AnimeTheme $animetheme
 * @property int $entry_id
 * @property string|null $episodes
 * @property string|null $notes
 * @property bool $nsfw
 * @property bool $spoiler
 * @property int $theme_id
 * @property int|null $version
 * @property Collection<int, Video> $videos
 *
 * @method static AnimeThemeEntryFactory factory(...$parameters)
 */
class AnimeThemeEntry extends BaseModel implements InteractsWithSchema
{
    use Reportable;
    use Searchable;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    final public const TABLE = 'anime_theme_entries';

    final public const ATTRIBUTE_EPISODES = 'episodes';
    final public const ATTRIBUTE_ID = 'entry_id';
    final public const ATTRIBUTE_NOTES = 'notes';
    final public const ATTRIBUTE_NSFW = 'nsfw';
    final public const ATTRIBUTE_SPOILER = 'spoiler';
    final public const ATTRIBUTE_THEME = 'theme_id';
    final public const ATTRIBUTE_VERSION = 'version';

    final public const RELATION_ANIME = 'animetheme.anime';
    final public const RELATION_ANIME_SHALLOW = 'anime';
    final public const RELATION_SONG = 'animetheme.song';
    final public const RELATION_SYNONYMS = 'animetheme.anime.animesynonyms';
    final public const RELATION_THEME = 'animetheme';
    final public const RELATION_THEME_GROUP = 'animetheme.group';
    final public const RELATION_VIDEOS = 'videos';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        AnimeThemeEntry::ATTRIBUTE_EPISODES,
        AnimeThemeEntry::ATTRIBUTE_NOTES,
        AnimeThemeEntry::ATTRIBUTE_NSFW,
        AnimeThemeEntry::ATTRIBUTE_SPOILER,
        AnimeThemeEntry::ATTRIBUTE_THEME,
        AnimeThemeEntry::ATTRIBUTE_VERSION,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => EntryCreated::class,
        'deleted' => EntryDeleted::class,
        'deleting' => EntryDeleting::class,
        'restored' => EntryRestored::class,
        'updated' => EntryUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = AnimeThemeEntry::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = AnimeThemeEntry::ATTRIBUTE_ID;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            AnimeThemeEntry::ATTRIBUTE_NSFW => 'boolean',
            AnimeThemeEntry::ATTRIBUTE_SPOILER => 'boolean',
            AnimeThemeEntry::ATTRIBUTE_VERSION => 'int',
        ];
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with([
            AnimeThemeEntry::RELATION_SYNONYMS,
            AnimeThemeEntry::RELATION_SONG,
        ]);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();
        $array['theme'] = $this->animetheme->toSearchableArray();

        // Overwrite version with readable format "V{#}"
        $array['version'] = Str::of(empty($this->version) ? '1' : $this->version)->prepend('V')->__toString();

        return $array;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        $this->loadMissing([
            AnimeThemeEntry::RELATION_ANIME,
            AnimeThemeEntry::RELATION_THEME,
            AnimeThemeEntry::RELATION_THEME_GROUP,
        ]);

        $theme = is_null($this->animetheme)
            ? $this->animetheme()->withoutGlobalScope(WithoutInsertSongScope::class)->first()
            : $this->animetheme;

        if (is_null($theme)) {
            return strval($this->getKey());
        }

        return Str::of($this->anime->name)
            ->append(' ')
            ->append($theme->type->localize())
            ->append($theme->type === ThemeType::IN ? '' : strval($theme->sequence ?? 1))
            ->append(empty($this->version) ? '' : "v$this->version")
            ->append($theme->group !== null ? '-'.$theme->group->slug : '')
            ->__toString();
    }

    /**
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        return "{$this->anime->getName()} {$this->animetheme->getName()}";
    }

    /**
     * Get the theme that owns the entry.
     *
     * @return BelongsTo<AnimeTheme, $this>
     */
    public function animetheme(): BelongsTo
    {
        return $this->belongsTo(AnimeTheme::class, AnimeThemeEntry::ATTRIBUTE_THEME);
    }

    /**
     * Get the videos linked in the theme entry.
     *
     * @return BelongsToMany<Video, $this>
     */
    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(
            Video::class,
            AnimeThemeEntryVideo::class,
            AnimeThemeEntry::ATTRIBUTE_ID,
            Video::ATTRIBUTE_ID
        )
            ->using(AnimeThemeEntryVideo::class)
            ->as(AnimeThemeEntryVideoResource::$wrap)
            ->withTimestamps();
    }

    /**
     * Get the anime that owns the entry through the theme.
     *
     * @return BelongsToThrough
     */
    public function anime(): BelongsToThrough
    {
        return $this->belongsToThrough(
            Anime::class,
            AnimeTheme::class,
            null,
            '',
            [
                Anime::class => Anime::ATTRIBUTE_ID,
                AnimeTheme::class => AnimeTheme::ATTRIBUTE_ID,
            ]
        );
    }

    /**
     * Get the schema for the model.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new EntrySchema();
    }
}
