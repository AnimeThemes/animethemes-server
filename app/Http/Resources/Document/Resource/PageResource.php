<?php

declare(strict_types=1);

namespace App\Http\Resources\Document\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Resources\BaseResource;
use App\Models\BaseModel;
use App\Models\Document\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class PageResource.
 *
 * @mixin Page
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

        if ($this->isAllowedField(Page::ATTRIBUTE_NAME)) {
            $result[Page::ATTRIBUTE_NAME] = $this->name;
        }

        if ($this->isAllowedField(Page::ATTRIBUTE_SLUG)) {
            $result[Page::ATTRIBUTE_SLUG] = $this->slug;
        }

        if ($this->isAllowedField(Page::ATTRIBUTE_BODY)) {
            $result[Page::ATTRIBUTE_BODY] = $this->body;
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
