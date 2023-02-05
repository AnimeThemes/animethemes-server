<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Admin\DumpSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Models\Admin\Dump;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class DumpResource.
 */
class DumpResource extends BaseResource
{
    final public const ATTRIBUTE_LINK = 'link';

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'dump';

    /**
     * Create a new resource instance.
     *
     * @param  Dump | MissingValue | null  $dump
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(Dump|MissingValue|null $dump, ReadQuery $query)
    {
        parent::__construct($dump, $query);
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new DumpSchema();
    }
}
