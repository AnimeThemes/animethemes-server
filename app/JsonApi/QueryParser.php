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
            foreach ($fieldsParam as $type => $fieldList) {
                Arr::set($fields, $type, array_map('trim', explode(',', $fieldList)));
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
            $includes = array_map('trim', explode(',', $includeParam));
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
            foreach ($filterParam as $field => $filterList) {
                Arr::set($filters, $field, array_map('trim', explode(',', $filterList)));
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
            $search = trim($parameters[self::PARAM_SEARCH]);
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
}
