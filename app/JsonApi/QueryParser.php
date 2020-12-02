<?php

namespace App\JsonApi;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class QueryParser
{

    public const PARAM_INCLUDE = 'include';

    public const PARAM_FIELDS = 'fields';

    public const PARAM_SORT = 'sort';

    public const PARAM_FILTER = 'filter';

    public const PARAM_SEARCH = 'q';

    public const PARAM_AMOUNT = 'amount';

    private const AMOUNT_MAX = 30;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var array|null
     */
    private $includes;

    /**
     * @var array
     */
    private $resourceIncludes;

    /**
     * @var array
     */
    private $sorts;

    /**
     * @var array
     */
    private $filters;

    /**
     * @var string
     */
    private $search;

    /**
     * @var int
     */
    private $amount;

    /**
     * @param array $parameters
     */
    public function __construct($parameters)
    {
        $this->fields = $this->parseFields($parameters);
        $this->includes = $this->parseIncludes($parameters);
        $this->resourceIncludes = $this->parseResourceIncludes($parameters);
        $this->sorts = $this->parseSorts($parameters);
        $this->filters = $this->parseFilters($parameters);
        $this->search = $this->parseSearch($parameters);
        $this->amount = $this->parseAmount($parameters);
    }

    /**
     * Parse sparse fields from parameters.
     *
     * @param array $parameters
     * @return array
     */
    private function parseFields($parameters)
    {
        $fields = [];

        if (Arr::exists($parameters, self::PARAM_FIELDS)) {
            $fieldsParam = $parameters[self::PARAM_FIELDS];
            if (Arr::accessible($fieldsParam) && Arr::isAssoc($fieldsParam)) {
                foreach ($fieldsParam as $type => $fieldList) {
                    Arr::set($fields, $type, array_map('trim', explode(',', $fieldList)));
                }
            }
        }

        return $fields;
    }

    /**
     * Get sparse fields.
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Parse includes from parameters.
     *
     * @param array $parameters
     * @return array|null
     */
    private function parseIncludes($parameters)
    {
        $includes = null;

        if (Arr::exists($parameters, self::PARAM_INCLUDE)) {
            $includeParam = $parameters[self::PARAM_INCLUDE];
            if (! Arr::accessible($includeParam)) {
                $includes = array_map('trim', explode(',', $includeParam));
            }
        }

        return $includes;
    }

    /**
     * Parse includes by resource from parameters.
     *
     * @param array $parameters
     * @return array
     */
    private function parseResourceIncludes($parameters)
    {
        $resourceIncludes = [];

        if (Arr::exists($parameters, self::PARAM_INCLUDE)) {
            $includeParam = $parameters[self::PARAM_INCLUDE];
            if (Arr::accessible($includeParam) && Arr::isAssoc($includeParam)) {
                foreach ($includeParam as $type => $includeList) {
                    Arr::set($resourceIncludes, $type, array_map('trim', explode(',', $includeList)));
                }
            }
        }

        return $resourceIncludes;
    }

    /**
     * Parse sorts from parameters.
     *
     * @param array $parameters
     * @return array
     */
    private function parseSorts($parameters)
    {
        $sorts = [];

        if (Arr::exists($parameters, self::PARAM_SORT)) {
            $sortParam = $parameters[self::PARAM_SORT];
            if (! Arr::accessible($sortParam)) {
                $sortValues = array_map('trim', explode(',', $sortParam));
                foreach ($sortValues as $orderAndField) {
                    switch ($orderAndField[0]) {
                        case '-':
                            $isAsc = false;
                            $field = substr($orderAndField, 1);
                            break;
                        case '+':
                            $isAsc = true;
                            $field = substr($orderAndField, 1);
                            break;
                        default:
                            $isAsc = true;
                            $field = $orderAndField;
                            break;
                    }

                    Arr::set($sorts, $field, $isAsc);
                }
            }
        }

        return $sorts;
    }

    /**
     * Get sorts.
     *
     * @return array
     */
    public function getSorts()
    {
        return $this->sorts;
    }

    /**
     * Parse filters from parameters.
     *
     * @param array $parameters
     * @return array
     */
    private function parseFilters($parameters)
    {
        $filters = [];

        if (Arr::exists($parameters, self::PARAM_FILTER)) {
            $filterParam = $parameters[self::PARAM_FILTER];
            if (Arr::accessible($filterParam) && Arr::isAssoc($filterParam)) {
                foreach ($filterParam as $field => $filterList) {
                    Arr::set($filters, $field, array_map('trim', explode(',', $filterList)));
                }
            }
        }

        return $filters;
    }

    /**
     * Parse search term from parameters.
     *
     * @param array $parameters
     * @return string
     */
    private function parseSearch($parameters)
    {
        $search = '';

        if (Arr::exists($parameters, self::PARAM_SEARCH)) {
            $searchParam = $parameters[self::PARAM_SEARCH];
            if (! Arr::accessible($searchParam)) {
                $search = trim($searchParam);
            }
        }

        return $search;
    }

    /**
     * Check if search term is provided in request.
     *
     * @return bool
     */
    public function hasSearch()
    {
        return ! empty($this->search);
    }

    /**
     * Get search term from parameters.
     *
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * Parse amount number from parameters.
     *
     * @param array $parameters
     * @return int
     */
    public function parseAmount($parameters)
    {
        $amount = 1;

        if (Arr::exists($parameters, self::PARAM_AMOUNT)) {
            $amountParam = $parameters[self::PARAM_AMOUNT];
            if (! Arr::accessible($amountParam)) {
                $amountStr = trim($amountParam);
                if (is_numeric($amountStr) && intval($amountStr) > 0) {
                    // limit to maximum specified in AMOUNT_MAX
                    $amount = intval($amountStr) > self::AMOUNT_MAX ? self::AMOUNT_MAX : intval($amountStr);
                };
            }
        }

        return $amount;
    }

    /**
     * Get amount number from parameters.
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Determine if field should be included in the response for this type.
     *
     * @param string $type
     * @param string $field
     *
     * @return bool
     */
    public function isAllowedField($type, $field)
    {
        // If we aren't filtering this type, include all fields
        if (! Arr::exists($this->fields, $type)) {
            return true;
        }

        // If there are no allowed fields for this type, include all fields
        $allowedFields = Arr::get($this->fields, $type);
        if (empty($allowedFields)) {
            return true;
        }

        // Is field included for this type
        return in_array($field, $allowedFields);
    }

    /**
     * Check if filter exists for field.
     *
     * @param string $field
     * @return bool
     */
    public function hasFilter($field)
    {
        return Arr::exists($this->filters, $field);
    }

    /**
     * Get filter values for field.
     *
     * @param string $field
     * @return array
     */
    public function getFilter($field)
    {
        return Arr::get($this->filters, $field);
    }

    /**
     * Get filter values for enum field, converting keys from query string to int values.
     *
     * @param string $field
     * @param  string $enumClass
     * @return array
     */
    public function getEnumFilter($field, $enumClass)
    {
        return array_map(function ($enumKey) use ($enumClass) {
            if ($enumClass::hasKey(Str::upper($enumKey))) {
                return $enumClass::getValue(Str::upper($enumKey));
            }

            return -1;
        }, $this->getFilter($field));
    }

    /**
     * Get filter values for boolean field.
     *
     * @param string $field
     * @return array
     */
    public function getBooleanFilter($field)
    {
        return array_map(function ($filterValue) {
            return filter_var($filterValue, FILTER_VALIDATE_BOOLEAN);
        }, $this->getFilter($field));
    }

    /**
     * The validated include paths used to eager load relations.
     *
     * @param array $allowedIncludePaths
     * @return array
     */
    public function getIncludePaths($allowedIncludePaths)
    {
        // If include paths are not specified, return full list of allowed include paths
        if (is_null($this->includes)) {
            return $allowedIncludePaths;
        }

        // Return list of include paths that are contained in the list of allowed include paths
        return array_intersect($this->includes, $allowedIncludePaths);
    }

    /**
     * The validated include paths used to eager load relations for the specified type.
     *
     * @param array $allowedResourceIncludePaths
     * @param string $type
     * @return array
     */
    public function getResourceIncludePaths($allowedResourceIncludePaths, $type)
    {
        // If we are not specifying include paths for this type, include all default relations
        if (! Arr::exists($this->resourceIncludes, $type)) {
            return $allowedResourceIncludePaths;
        }

        // Return list of include paths that are contained in the list of allowed include paths for this type
        $resourceTypeIncludes = Arr::get($this->resourceIncludes, $type);

        return array_intersect($resourceTypeIncludes, $allowedResourceIncludePaths);
    }
}
