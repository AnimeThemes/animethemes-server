<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Collection;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Resources\SearchableCollection;
use App\Http\Resources\Wiki\Anime\Resource\ThemeResource;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Http\Request;

/**
 * Class ThemeCollection.
 */
class ThemeCollection extends SearchableCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animethemes';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = AnimeTheme::class;

    /**
     * Transform the resource into a JSON array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(fn (AnimeTheme $theme) => ThemeResource::make($theme, $this->query))->all();
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public static function schema(): Schema
    {
        return new ThemeSchema();
    }
}
