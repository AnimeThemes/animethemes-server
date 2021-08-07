<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

use Illuminate\Support\Collection;
use BenSampo\Enum\Enum;
use Illuminate\Support\Str;

/**
 * Class EnumFilter.
 */
abstract class EnumFilter extends Filter
{
    /**
     * The Enum class string.
     *
     * @var class-string<Enum>
     */
    protected string $enumClass;

    /**
     * Create a new filter instance.
     *
     * @param Collection $criteria
     * @param string $key
     * @param class-string<Enum> $enumClass
     */
    public function __construct(Collection $criteria, string $key, string $enumClass)
    {
        parent::__construct($criteria, $key);
        $this->enumClass = $enumClass;
    }

    /**
     * Convert filter values to enum values from key.
     *
     * @param array $filterValues
     * @return array
     */
    protected function convertFilterValues(array $filterValues): array
    {
        return array_map(
            function (string $filterValue) {
                return $this->enumClass::getValue(Str::upper($filterValue));
            },
            $filterValues
        );
    }

    /**
     * Get only filter values that are valid enum options.
     *
     * @param array $filterValues
     * @return array
     */
    protected function getValidFilterValues(array $filterValues): array
    {
        return array_values(
            array_filter(
                $filterValues,
                function (string $filterValue) {
                    return $this->enumClass::hasKey(Str::upper($filterValue));
                }
            )
        );
    }

    /**
     * Determine if all enum options have been specified.
     *
     * @param array $filterValues
     * @return bool
     */
    protected function isAllFilterValues(array $filterValues): bool
    {
        return count($filterValues) === count($this->enumClass::getInstances());
    }
}
