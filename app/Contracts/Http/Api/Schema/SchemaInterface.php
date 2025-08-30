<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Schema;

use App\Contracts\Http\Api\Field\FieldInterface;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Sort\Sort;

interface SchemaInterface
{
    public function type(): string;

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array;

    /**
     * @return FieldInterface[]
     */
    public function fields(): array;

    /**
     * @return Filter[]
     */
    public function filters(): array;

    /**
     * @return Sort[]
     */
    public function sorts(): array;
}
