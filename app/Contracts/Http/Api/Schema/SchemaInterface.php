<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Schema;

use App\Contracts\Http\Api\Field\FieldInterface;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Sort\Sort;

interface SchemaInterface
{
    /**
     * Get the type of the resource.
     */
    public function type(): string;

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array;

    /**
     * Get the direct fields of the resource.
     *
     * @return FieldInterface[]
     */
    public function fields(): array;

    /**
     * Get the filters of the resource.
     *
     * @return Filter[]
     */
    public function filters(): array;

    /**
     * Get the sorts of the resource.
     *
     * @return Sort[]
     */
    public function sorts(): array;
}
