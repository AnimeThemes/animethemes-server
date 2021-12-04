<?php

declare(strict_types=1);

namespace App\Http\Resources\Config\Resource;

use App\Constants\Config\WikiConstants;
use App\Http\Api\Query;
use App\Http\Api\Schema\Config\WikiSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Models\Wiki\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Arr;

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
     * @param  Query  $query
     * @return void
     */
    public function __construct(Query $query)
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
        // Every attribute may query the database for the setting, so we will proactively check sparse fieldsets.
        $result = [];

        if ($this->isAllowedField(WikiConstants::FEATURED_THEME_SETTING)) {
            $video = Video::query()->firstWhere(
                Video::ATTRIBUTE_BASENAME,
                config(WikiConstants::FEATURED_THEME_SETTING_QUALIFIED)
            );
            Arr::set($result,
                WikiConstants::FEATURED_THEME_SETTING,
                $video instanceof Video ? route('video.show', ['video' => $video]) : null
            );
        }

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public static function schema(): Schema
    {
        return new WikiSchema();
    }
}
