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

    public const PARAM_LIMIT = 'limit';

    /**
     * @var array
     */
    private $fields;

    /**
     * @var array
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
    private $limit;

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
        $this->limit = $this->parseLimit($parameters);
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
     * @return array
     */
    private function parseIncludes($parameters)
    {
        $includes = [];

        if (Arr::exists($parameters, self::PARAM_INCLUDE)) {
            $includeParam = $parameters[self::PARAM_INCLUDE];
            if (! Arr::accessible($includeParam)) {
                $includes = array_map('trim', explode(',', $includeParam));
            }
        }

        return $includes;
    }

    /**
     * Get includes.
     *
     * @return array
     */
    public function getIncludes()
    {
        return $this->includes;
    }

    /**
     * Parse includes by resource from parameters.
     *
     * @param array $parameters
     * @return array
     */
    private function parseResourceIncludes($parameters)
    {
        $includes = [];

        if (Arr::exists($parameters, self::PARAM_INCLUDE)) {
            $includeParam = $parameters[self::PARAM_INCLUDE];
            if (Arr::accessible($includeParam) && Arr::isAssoc($includeParam)) {
                foreach ($includeParam as $type => $includeList) {
                    Arr::set($includes, $type, array_map('trim', explode(',', $includeList)));
                }
            }
        }

        return $includes;
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
     * Parse page limit from parameters.
     *
     * @param array $parameters
     * @return int
     */
    private function parseLimit($parameters)
    {
        $limit = 0;

        if (Arr::exists($parameters, self::PARAM_LIMIT)) {
            $limitParam = $parameters[self::PARAM_LIMIT];
            if (! Arr::accessible($limitParam)) {
                $limit = intval($limitParam);
            }
        }

        return $limit;
    }

    /**
     * Get the number of resources to return per page.
     * Acceptable range is [1-100]. Default is 100.
     *
     * @param  int  $limit
     * @return int
     */
    public function getPerPageLimit($limit = 100)
    {
        if ($this->limit <= 0 || $this->limit > $limit) {
            return $limit;
        }

        return $this->limit;
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
     * @return array
     */
    public function getIncludePaths($allowedIncludePaths)
    {
        // If include paths are not specified, return full list of allowed include paths
        if (empty($this->includes)) {
            return $allowedIncludePaths;
        }

        // If no include paths are contained in the list of allowed include paths,
        // return the full list of allowed include paths
        $validIncludePaths = array_intersect($this->includes, $allowedIncludePaths);
        if (empty($validIncludePaths)) {
            return $allowedIncludePaths;
        }

        // Return list of include paths that are contained in the list of allowed include paths
        return $validIncludePaths;
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

        // If there are no include paths for this type, include all default relations
        $resourceTypeIncludes = Arr::get($this->resourceIncludes, $type);
        if (empty($resourceTypeIncludes)) {
            return $allowedResourceIncludePaths;
        }

        // If no include paths are contained in the list of allowed include paths for this type,
        // return the full list of allowed include paths
        $validResourceIncludePaths = array_intersect($resourceTypeIncludes, $allowedResourceIncludePaths);
        if (empty($validResourceIncludePaths)) {
            return $allowedResourceIncludePaths;
        }

        // Return list of include paths that are contained in the list of allowed include paths for this type
        return $validResourceIncludePaths;
    }
}
