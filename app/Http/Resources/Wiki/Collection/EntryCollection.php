<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Filter\Base\CreatedAtFilter;
use App\Http\Api\Filter\Base\DeletedAtFilter;
use App\Http\Api\Filter\Base\TrashedFilter;
use App\Http\Api\Filter\Base\UpdatedAtFilter;
use App\Http\Api\Filter\Wiki\Entry\EntryNsfwFilter;
use App\Http\Api\Filter\Wiki\Entry\EntrySpoilerFilter;
use App\Http\Api\Filter\Wiki\Entry\EntryVersionFilter;
use App\Http\Resources\SearchableCollection;
use App\Http\Resources\Wiki\Resource\EntryResource;
use App\Models\Wiki\Entry;
use Illuminate\Http\Request;

/**
 * Class EntryCollection.
 */
class EntryCollection extends SearchableCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'entries';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Entry::class;

    /**
     * Transform the resource into a JSON array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (Entry $entry) {
            return EntryResource::make($entry, $this->parser);
        })->all();
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

    /**
     * The sort field names a client is allowed to request.
     *
     * @return array
     */
    public static function allowedSortFields(): array
    {
        return [
            'entry_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'version',
            'nsfw',
            'spoiler',
            'theme_id',
        ];
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @return string[]
     */
    public static function filters(): array
    {
        return [
            EntryNsfwFilter::class,
            EntrySpoilerFilter::class,
            EntryVersionFilter::class,
            CreatedAtFilter::class,
            UpdatedAtFilter::class,
            DeletedAtFilter::class,
            TrashedFilter::class,
        ];
    }
}