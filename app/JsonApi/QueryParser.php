<?php

namespace App\JsonApi;

use App\JsonApi\Condition\Condition;
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
    protected $fields;

    /**
     * @var array|null
     */
    protected $includes;

    /**
     * @var array
     */
    protected $sorts;

    /**
     * @var \App\JsonApi\Condition\Condition[]
     */
    protected $conditions;

    /**
     * @var string
     */
    protected $search;

    /**
     * @var int
     */
    protected $limit;

    /**
     * Create a new query parser instance.
     *
     * @param array $parameters
     */
    final public function __construct($parameters = [])
    {
        $this->fields = $this->parseFields($parameters);
        $this->includes = $this->parseIncludes($parameters);
        $this->sorts = $this->parseSorts($parameters);
        $this->conditions = $this->parseConditions($parameters);
        $this->search = $this->parseSearch($parameters);
        $this->limit = $this->parseLimit($parameters);
    }

    /**
     * Create a new query parser instance.
     *
     * @param mixed ...$parameters
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
    protected function parseFields($parameters)
    {
        $fields = [];

        if (Arr::exists($parameters, self::PARAM_FIELDS)) {
            $fieldsParam = $parameters[self::PARAM_FIELDS];
            if (Arr::accessible($fieldsParam) && Arr::isAssoc($fieldsParam)) {
                foreach ($fieldsParam as $type => $fieldList) {
                    if (! Arr::accessible($fieldList)) {
                        Arr::set($fields, $type, array_map('trim', explode(',', $fieldList)));
                    }
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
    protected function parseIncludes($parameters)
    {
        $includes = null;

        if (Arr::exists($parameters, self::PARAM_INCLUDE)) {
            $includeParam = $parameters[self::PARAM_INCLUDE];
            if (! Arr::accessible($includeParam)) {
                Arr::set($includes, '', array_map('trim', explode(',', $includeParam)));
            }
            if (Arr::accessible($includeParam) && Arr::isAssoc($includeParam)) {
                foreach ($includeParam as $type => $includeList) {
                    if (! Arr::accessible($includeList)) {
                        Arr::set($includes, $type, array_map('trim', explode(',', $includeList)));
                    }
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
    protected function parseSorts($parameters)
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
     * Parse filter conditions from parameters.
     *
     * @param array $parameters
     * @return \App\JsonApi\Condition\Condition[]
     */
    protected function parseConditions($parameters)
    {
        $conditions = [];

        if (Arr::exists($parameters, self::PARAM_FILTER)) {
            $filterParam = $parameters[self::PARAM_FILTER];
            if (Arr::accessible($filterParam) && Arr::isAssoc($filterParam)) {
                foreach (Arr::dot($filterParam) as $filterCondition => $filterValues) {
                    if ($filterValues !== null) {
                        $conditions[] = Condition::make($filterCondition, $filterValues);
                    }
                }
            }
        }

        return $conditions;
    }

    /**
     * Parse search term from parameters.
     *
     * @param array $parameters
     * @return string
     */
    protected function parseSearch($parameters)
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
    protected function parseLimit($parameters)
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
     * Acceptable range is [1-15]. Default is 15.
     *
     * @param int $limit
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
     * Check if conditions exist for field.
     *
     * @param string $field
     * @return bool
     */
    public function hasCondition($field)
    {
        return ! empty($this->getConditions($field));
    }

    /**
     * Get conditions for field.
     *
     * @param string $field
     * @return \App\JsonApi\Condition\Condition[]
     */
    public function getConditions($field)
    {
        return array_filter($this->conditions, function (Condition $condition) use ($field) {
            return $field === $condition->getField();
        });
    }

    /**
     * The validated include paths used to eager load relations.
     *
     * @param array $allowedIncludePaths
     * @param string $type
     * @return array
     */
    public function getIncludePaths($allowedIncludePaths, $type = '')
    {
        // Return list of include paths that are contained in the list of allowed include paths for this type
        $resourceTypeIncludes = [];
        if (Arr::has($this->includes, $type)) {
            $resourceTypeIncludes = Arr::get($this->includes, $type);
        } else {
            $resourceTypeIncludes = Arr::get($this->includes, '');
        }

        return collect($resourceTypeIncludes)->intersect($allowedIncludePaths)->all();
    }
}
