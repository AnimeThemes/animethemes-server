<?php

namespace App\Http\Resources;

use App\Concerns\JsonApi\PerformsResourceQuery;

/**
 * @OA\Schema(
 *     title="Announcement",
 *     description="Announcement Resource",
 *     type="object",
 *     @OA\Property(property="id",type="integer",description="Primary Key",example=1),
 *     @OA\Property(property="content",type="string",description="The Announcement Text",example="There will be a period of scheduled downtime..."),
 *     @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:43:02.000000Z"),
 *     @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:43:02.000000Z"),
 * )
 */
class AnnouncementResource extends BaseResource
{
    use PerformsResourceQuery;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'announcement';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->announcement_id),
            'content' => $this->when($this->isAllowedField('content'), $this->content),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
        ];
    }
}
