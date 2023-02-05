<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Admin\AnnouncementSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Models\Admin\Announcement;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class AnnouncementResource.
 */
class AnnouncementResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'announcement';

    /**
     * Create a new resource instance.
     *
     * @param  Announcement | MissingValue | null  $announcement
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(Announcement|MissingValue|null $announcement, ReadQuery $query)
    {
        parent::__construct($announcement, $query);
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new AnnouncementSchema();
    }
}
