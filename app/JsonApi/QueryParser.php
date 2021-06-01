<?php

declare(strict_types=1);

namespace App\JsonApi;

use App\JsonApi\Condition\Condition;
use Illuminate\Support\Arr;

/**
 * Class QueryParser
 * @package App\JsonApi
 */
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
     * The list of fields to be included per type.
     *
     * @var array
     */
    protected array $fields;

    /**
     * The list of include paths per type.
     *
     * @var array|null
     */
    protected ?array $includes;

    /**
     * The list of fields and direction to base sorting on.
     *
     * @var array
     */
    protected array $sorts;

    /**
     * The list of filter conditions to apply to the query.
     *
     * @var Condition[]
     */
    protected array $conditions;

    /**
     * The search query, if applicable..
     *
     * @var string
     */
    protected string $search;

    /**
     * The result size limit, if applicable.
     *
     * @var int
     */
    protected int $limit;

    /**
     * Create a new query parser instance.
     *
     * @param array $parameters
     */
    final public function __construct(array $parameters = [])
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
    public static function make(...$parameters): static
    {
        return new static(...$parameters);
    }

    /**
     * Parse sparse fields from parameters.
     *
     * @param array $parameters
     * @return array
     */
    protected function parseFields(array $parameters): array
    {
        $fields = [];

        if (Arr::exists($parameters, self::PARAM_FIELDS)) {
            $fieldsParam = $parameters[self::PARAM_FIELDS];
            if (Arr::accessible($fieldsParam) && Arr::isAssoc($fieldsParam)) {
                foreach ($fieldsParam as $type => $fieldList) {
                    if ($fieldList !== null && ! Arr::accessible($fieldList)) {
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
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Parse includes from parameters.
     *
     * @param array $parameters
     * @return array|null
     */
    protected function parseIncludes(array $parameters): ?array
    {
        $includes = null;

        if (Arr::exists($parameters, self::PARAM_INCLUDE)) {
            $includeParam = $parameters[self::PARAM_INCLUDE];
            if ($includeParam !== null && ! Arr::accessible($includeParam)) {
                Arr::set($includes, '', array_map('trim', explode(',', $includeParam)));
            }
            if (Arr::accessible($includeParam) && Arr::isAssoc($includeParam)) {
                foreach ($includeParam as $type => $includeList) {
                    if ($includeList !== null && ! Arr::accessible($includeList)) {
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
    protected function parseSorts(array $parameters): array
    {
        $sorts = [];

        if (Arr::exists($parameters, self::PARAM_SORT)) {
            $sortParam = $parameters[self::PARAM_SORT];
            if ($sortParam !== null && ! Arr::accessible($sortParam)) {
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
    public function getSorts(): array
    {
        return $this->sorts;
    }

    /**
     * Parse filter conditions from parameters.
     *
     * @param array $parameters
     * @return Condition[]
     */
    protected function parseConditions(array $parameters): array
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
    protected function parseSearch(array $parameters): string
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
    public function hasSearch(): bool
    {
        return ! empty($this->search);
    }

    /**
     * Get search term from parameters.
     *
     * @return string
     */
    public function getSearch(): string
    {
        return $this->search;
    }

    /**
     * Parse page limit from parameters.
     *
     * @param array $parameters
     * @return int
     */
    protected function parseLimit(array $parameters): int
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
    public function getLimit(int $limit = self::DEFAULT_LIMIT): int
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
    public function isAllowedField(string $type, string $field): bool
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
    public function hasCondition(string $field): bool
    {
        return ! empty($this->getConditions($field));
    }

    /**
     * Get conditions for field.
     *
     * @param string $field
     * @return Condition[]
     */
    public function getConditions(string $field): array
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
    public function getIncludePaths(array $allowedIncludePaths, string $type = ''): array
    {
        // Return list of include paths that are contained in the list of allowed include paths for this type
        $resourceTypeIncludes = Arr::get($this->includes, $type, Arr::get($this->includes, ''));

        return collect($resourceTypeIncludes)->intersect($allowedIncludePaths)->all();
    }
}
