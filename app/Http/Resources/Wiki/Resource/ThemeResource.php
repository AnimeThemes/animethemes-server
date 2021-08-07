<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\EntryCollection;
use App\Models\Wiki\Theme;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class ThemeResource.
 *
 * @mixin Theme
 */
class ThemeResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'theme';

    /**
     * Create a new resource instance.
     *
     * @param Theme | MissingValue | null $theme
     * @param Query $query
     * @return void
     */
    public function __construct(Theme | MissingValue | null $theme, Query $query)
    {
        parent::__construct($theme, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->theme_id),
            'type' => $this->when($this->isAllowedField('type'), $this->type?->description),
            'sequence' => $this->when($this->isAllowedField('sequence'), $this->sequence),
            'group' => $this->when($this->isAllowedField('group'), $this->group),
            'slug' => $this->when($this->isAllowedField('slug'), $this->slug),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
            'anime' => AnimeResource::make($this->whenLoaded('anime'), $this->query),
            'song' => SongResource::make($this->whenLoaded('song'), $this->query),
            'entries' => EntryCollection::make($this->whenLoaded('entries'), $this->query),
        ];
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedIncludePaths(): array
    {
        return [
            'anime',
            'anime.images',
            'entries',
            'entries.videos',
            'song',
            'song.artists',
        ];
    }
}
