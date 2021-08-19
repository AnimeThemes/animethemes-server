<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Theme\Resource;

use App\Http\Api\Query;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Resource\ThemeResource;
use App\Http\Resources\Wiki\Collection\VideoCollection;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class EntryResource.
 *
 * @mixin AnimeThemeEntry
 */
class EntryResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animethemeentry';

    /**
     * Create a new resource instance.
     *
     * @param AnimeThemeEntry | MissingValue | null $entry
     * @param Query $query
     * @return void
     */
    public function __construct(AnimeThemeEntry | MissingValue | null $entry, Query $query)
    {
        parent::__construct($entry, $query);
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
            'id' => $this->when($this->isAllowedField('id'), $this->entry_id),
            'version' => $this->when($this->isAllowedField('version'), $this->version),
            'episodes' => $this->when($this->isAllowedField('episodes'), $this->episodes),
            'nsfw' => $this->when($this->isAllowedField('nsfw'), $this->nsfw),
            'spoiler' => $this->when($this->isAllowedField('spoiler'), $this->spoiler),
            'notes' => $this->when($this->isAllowedField('notes'), $this->notes),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
            'animetheme' => ThemeResource::make($this->whenLoaded('animetheme'), $this->query),
            'videos' => VideoCollection::make($this->whenLoaded('videos'), $this->query),
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
            'animetheme',
            'animetheme.anime',
            'videos',
        ];
    }
}
