<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Resources\BaseResource;
use App\Models\Admin\Dump;
use App\Models\BaseModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class DumpResource.
 *
 * @mixin Dump
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

        if ($this->isAllowedField(Dump::ATTRIBUTE_PATH)) {
            $result[Dump::ATTRIBUTE_PATH] = $this->path;
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

        if ($this->isAllowedField(DumpResource::ATTRIBUTE_LINK)) {
            $result[DumpResource::ATTRIBUTE_LINK] = route('dump.show', $this);
        }

        return $result;
    }
}
