<?php

namespace App\JsonApi;

use Illuminate\Support\Arr;

class QueryParser
{
    public const PARAM_INCLUDE = 'include';

    public const PARAM_FIELDS = 'fields';

    public const PARAM_SORT = 'sort';

    public const PARAM_FILTER = 'filter';

    public const PARAM_SEARCH = 'q';

    public const PARAM_LIMIT = 'limit';

    public const DEFAULT_LIMIT = 15;

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
    private $limit;

    /**
     * Create a new query parser instance.
     *
     * @param array $parameters
     */
    final public function __construct($parameters = [])
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
     * Create a new query parser instance.
     *
     * @param  mixed  ...$parameters
     * @return static
     */
    public static function make(...$parameters)
    {
        return new static(...$parameters);
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
     * Parse page limit from parameters.
     *
     * @param array $parameters
     * @return int
     */
    private function parseLimit($parameters)
    {
        $limit = 0;

        if (Arr::exists($parameters, self::PARAM_LIMIT)) {
            $limit = intval($parameters[self::PARAM_LIMIT]);
        }

        return $limit;
    }

    /**
     * Get the number of resources to return per page.
     * Acceptable range is [1-15]. Default is 15.
     *
     * @param  int  $limit
     * @return int
     */
    public function getLimit($limit = self::DEFAULT_LIMIT)
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

        // Is field included for this type
        $allowedFields = Arr::get($this->fields, $type);

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
        return array_values(array_intersect($this->includes, $allowedIncludePaths));
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

        return array_values(array_intersect($resourceTypeIncludes, $allowedResourceIncludePaths));
    }
}
