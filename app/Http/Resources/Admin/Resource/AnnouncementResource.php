<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Resource;

use App\Http\Api\QueryParser;
use App\Http\Resources\BaseResource;
use App\Models\Admin\Announcement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class AnnouncementResource.
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
