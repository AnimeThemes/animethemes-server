<?php

declare(strict_types=1);

namespace App\Http\Resources\Document\Resource;

use App\Http\Api\Query\Query;
use App\Http\Resources\BaseResource;
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
     * @param  Query  $query
     * @return void
     */
    public function __construct(Page|MissingValue|null $page, Query $query)
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
        return [
            BaseResource::ATTRIBUTE_ID => $this->when($this->isAllowedField(BaseResource::ATTRIBUTE_ID), $this->getKey()),
            Page::ATTRIBUTE_NAME => $this->when($this->isAllowedField(Page::ATTRIBUTE_NAME), $this->name),
            Page::ATTRIBUTE_SLUG => $this->when($this->isAllowedField(Page::ATTRIBUTE_SLUG), $this->slug),
        ];
    }
}
