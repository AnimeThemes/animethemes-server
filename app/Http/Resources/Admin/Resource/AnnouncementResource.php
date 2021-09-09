<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Resource;

use App\Http\Api\Query;
use App\Http\Resources\BaseResource;
use App\Models\Admin\Announcement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class AnnouncementResource.
 *
 * @mixin Announcement
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
     * @param  Query  $query
     * @return void
     */
    public function __construct(Announcement|MissingValue|null $announcement, Query $query)
    {
        parent::__construct($announcement, $query);
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
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->announcement_id),
            'content' => $this->when($this->isAllowedField('content'), $this->content),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
        ];
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedIncludePaths(): array
    {
        return [];
    }
}
