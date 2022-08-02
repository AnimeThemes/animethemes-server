<?php

declare(strict_types=1);

namespace App\Http\Resources\Config\Resource;

use App\Constants\Config\WikiConstants;
use App\Http\Api\Query\ReadQuery;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Pivot\Resource\AnimeThemeEntryVideoResource;
use App\Pivots\AnimeThemeEntryVideo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Facades\Config;

/**
 * Class WikiResource.
 */
class WikiResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'wiki';

    /**
     * Create a new resource instance.
     *
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(ReadQuery $query)
    {
        parent::__construct(new MissingValue(), $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        $result = [];

        if ($this->isAllowedField(WikiConstants::FEATURED_THEME_SETTING)) {
            /** @var AnimeThemeEntryVideo|null $pivot */
            $pivot = AnimeThemeEntryVideo::query()
                ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, Config::get('wiki.featured_entry'))
                ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, Config::get('wiki.featured_video'))
                ->with([
                    AnimeThemeEntryVideo::RELATION_ANIME,
                    AnimeThemeEntryVideo::RELATION_ARTISTS,
                    AnimeThemeEntryVideo::RELATION_IMAGES,
                    AnimeThemeEntryVideo::RELATION_SONG,
                    AnimeThemeEntryVideo::RELATION_VIDEO,
                ])
                ->first();

            $result[WikiConstants::FEATURED_THEME_SETTING] = new AnimeThemeEntryVideoResource($pivot, $this->query);
        }

        return $result;
    }
}
