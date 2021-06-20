<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Resource;

use App\Http\Api\QueryParser;
use App\Http\Resources\BaseResource;
use App\Models\Admin\Announcement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

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
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'announcement';

    /**
     * Create a new resource instance.
     *
     * @param Announcement | MissingValue | null $announcement
     * @param QueryParser $parser
     * @return void
     */
    public function __construct(Announcement | MissingValue | null $announcement, QueryParser $parser)
    {
        parent::__construct($announcement, $parser);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
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
}
