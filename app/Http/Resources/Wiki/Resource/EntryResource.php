<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\QueryParser;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\VideoCollection;
use App\Models\Wiki\Entry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class EntryResource.
 */
class EntryResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'entry';

    /**
     * Create a new resource instance.
     *
     * @param Entry | MissingValue | null $entry
     * @param QueryParser $parser
     * @return void
     */
    public function __construct(Entry | MissingValue | null $entry, QueryParser $parser)
    {
        parent::__construct($entry, $parser);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->entry_id),
            'version' => $this->when($this->isAllowedField('version'), $this->version === null ? '' : $this->version),
            'episodes' => $this->when($this->isAllowedField('episodes'), $this->episodes),
            'nsfw' => $this->when($this->isAllowedField('nsfw'), $this->nsfw),
            'spoiler' => $this->when($this->isAllowedField('spoiler'), $this->spoiler),
            'notes' => $this->when($this->isAllowedField('notes'), $this->notes),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
            'theme' => ThemeResource::make($this->whenLoaded('theme'), $this->parser),
            'videos' => VideoCollection::make($this->whenLoaded('videos'), $this->parser),
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
            'theme',
            'theme.anime',
            'videos',
        ];
    }
}
