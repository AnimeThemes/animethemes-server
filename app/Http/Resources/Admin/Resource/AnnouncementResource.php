<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Resource;

use App\Http\Api\Query\Query;
use App\Http\Resources\BaseResource;
use App\Models\Admin\Announcement;
use App\Models\BaseModel;
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
        $result = [];

        if ($this->isAllowedField(BaseResource::ATTRIBUTE_ID)) {
            $result[BaseResource::ATTRIBUTE_ID] = $this->getKey();
        }

        if ($this->isAllowedField(Announcement::ATTRIBUTE_CONTENT)) {
            $result[Announcement::ATTRIBUTE_CONTENT] = $this->content;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT)) {
            $result[BaseModel::ATTRIBUTE_CREATED_AT] = $this->created_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT)) {
            $result[BaseModel::ATTRIBUTE_UPDATED_AT] = $this->updated_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT)) {
            $result[BaseModel::ATTRIBUTE_DELETED_AT] = $this->deleted_at;
        }

        return $result;
    }
}
