<?php

declare(strict_types=1);

namespace App\Http\Api\Schema;

use App\Enums\Http\Api\Field\Category;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Field\DateField;
use App\Http\Api\Field\Field;
use App\Http\Api\Filter\EnumFilter;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Sort\RandomSort;
use App\Http\Api\Sort\Sort;
use App\Models\BaseModel;

/**
 * Class Schema.
 */
abstract class Schema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    abstract public function type(): string;

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    abstract public function allowedIncludes(): array;

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            new DateField(BaseModel::ATTRIBUTE_CREATED_AT),
            new DateField(BaseModel::ATTRIBUTE_UPDATED_AT),
            new DateField(BaseModel::ATTRIBUTE_DELETED_AT),
        ];
    }

    /**
     * Get the filters of the resource.
     *
     * @return Filter[]
     */
    public function filters(): array
    {
        $filters = [];

        foreach ($this->fields() as $field) {
            if ($field->getCategory()->is(Category::ATTRIBUTE())) {
                $filters[] = $field->getFilter();
            }
        }

        $filters[] = new EnumFilter(TrashedCriteria::PARAM_VALUE, TrashedStatus::class);

        return $filters;
    }

    /**
     * Get the sorts of the resource.
     *
     * @return Sort[]
     */
    public function sorts(): array
    {
        $sorts = [];

        foreach ($this->fields() as $field) {
            if ($field->getCategory()->is(Category::ATTRIBUTE())) {
                $sorts[] = $field->getSort();
            }
        }

        $sorts[] = new RandomSort();

        return $sorts;
    }
}
