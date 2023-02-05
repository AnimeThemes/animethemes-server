<?php

declare(strict_types=1);

namespace App\Http\Resources\Document\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Document\PageSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Models\Document\Page;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class PageResource.
 */
class PageResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'page';

    /**
     * Create a new resource instance.
     *
     * @param  Page | MissingValue | null  $page
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(Page|MissingValue|null $page, ReadQuery $query)
    {
        parent::__construct($page, $query);
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new PageSchema();
    }
}
