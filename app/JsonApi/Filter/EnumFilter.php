<?php

declare(strict_types=1);

namespace App\JsonApi\Filter;

use App\JsonApi\QueryParser;
use Illuminate\Support\Str;

/**
 * Class EnumFilter.
 */
abstract class EnumFilter extends Filter
{
    /**
     * The Enum class string.
     *
     * @var string
     */
    protected string $enumClass;

    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     * @param string $key
     * @param string $enumClass
     */
    public function __construct(QueryParser $parser, string $key, string $enumClass)
    {
        parent::__construct($parser, $key);
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
