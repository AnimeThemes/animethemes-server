<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

use App\Enums\BaseEnum;

/**
 * Class EnumFilter.
 */
class EnumFilter extends Filter
{
    /**
     * The Enum class string.
     *
     * @var class-string<BaseEnum>
     */
    protected string $enumClass;

    /**
     * Create a new filter instance.
     *
     * @param  string  $key
     * @param  string|null  $column
     * @param  class-string<BaseEnum>  $enumClass
     */
    public function __construct(string $key, string $enumClass, ?string $column = null)
    {
        parent::__construct($key, $column);
        $this->enumClass = $enumClass;
    }

    /**
     * Convert filter values to enum values from key.
     *
     * @param  array  $filterValues
     * @return array
     */
    protected function convertFilterValues(array $filterValues): array
    {
        return array_map(
            function (string $filterValue) {
                return $this->enumClass::fromDescription($filterValue)?->value;
            },
            $filterValues
        );
    }

    /**
     * Get only filter values that are valid enum options.
     *
     * @param  array  $filterValues
     * @return array
     */
    protected function getValidFilterValues(array $filterValues): array
    {
        return array_values(
            array_filter(
                $filterValues,
                function (string $filterValue) {
                    return $this->enumClass::fromDescription($filterValue) !== null;
                }
            )
        );
    }

    /**
     * Determine if all enum options have been specified.
     *
     * @param  array  $filterValues
     * @return bool
     */
    protected function isAllFilterValues(array $filterValues): bool
    {
        return count($filterValues) === count($this->enumClass::getInstances());
    }
}
