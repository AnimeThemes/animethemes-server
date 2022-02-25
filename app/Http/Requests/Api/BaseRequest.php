<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Rules\Api\DistinctIgnoringDirectionRule;
use App\Rules\Api\RandomSoleRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\ValidationRules\Rules\Delimited;

/**
 * Class BaseRequest.
 */
abstract class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return array_merge(
            $this->getFieldRules(),
            $this->getFilterRules(),
            $this->getIncludeRules(),
            $this->getPagingRules(),
            $this->getSearchRules(),
            $this->getSortRules(),
        );
    }

    /**
     * Restrict the allowed types for the parameter.
     *
     * @param  string  $param
     * @param  Collection  $types
     * @return array
     */
    protected function restrictAllowedTypes(string $param, Collection $types): array
    {
        return [
            $param => [
                'nullable',
                Str::of('array:')->append($types->join(','))->__toString(),
            ],
        ];
    }

    /**
     * Restrict the allowed values for the parameter.
     *
     * @param  string  $param
     * @param  Collection  $values
     * @param  array  $customRules
     * @return array
     */
    protected function restrictAllowedValues(string $param, Collection $values, array $customRules = []): array
    {
        return [
            $param => array_merge(
                ['sometimes', 'required', new Delimited(Rule::in($values))],
                $customRules,
            ),
        ];
    }

    /**
     * Prohibit the parameter.
     *
     * @param  string  $param
     * @return array
     */
    protected function prohibit(string $param): array
    {
        return [
            $param => [
                'prohibited',
            ],
        ];
    }

    /**
     * Optional parameter.
     *
     * @param  string  $param
     * @param  array  $customRules
     * @return array
     */
    protected function optional(string $param, array $customRules = []): array
    {
        return [
            $param => array_merge(
                ['sometimes', 'required'],
                $customRules,
            ),
        ];
    }

    /**
     * Require the parameter.
     *
     * @param  string  $param
     * @param  array  $customRules
     * @return array
     */
    protected function require(string $param, array $customRules = []): array
    {
        return [
            $param => array_merge(
                ['required'],
                $customRules,
            ),
        ];
    }

    /**
     * Get the field validation rules.
     *
     * @return array
     */
    protected function getFieldRules(): array
    {
        $schema = $this->schema();

        $types = collect($schema->type());

        $rules = $this->restrictAllowedFieldValues($schema);

        foreach ($schema->allowedIncludes() as $allowedIncludePath) {
            $relationSchema = $allowedIncludePath->schema();

            $types->push($relationSchema->type());

            $rules = $rules + $this->restrictAllowedFieldValues($relationSchema);
        }

        return $rules + $this->restrictAllowedTypes(FieldParser::param(), $types);
    }

    /**
     * Restrict the allowed values for the schema fields.
     *
     * @param  Schema  $schema
     * @return array[]
     */
    protected function restrictAllowedFieldValues(Schema $schema): array
    {
        return $this->restrictAllowedValues(
            Str::of(FieldParser::param())->append('.')->append($schema->type())->__toString(),
            collect($schema->fields())->map(fn (Field $field) => $field->getKey())
        );
    }

    /**
     * Get the filter validation rules.
     *
     * @return array
     */
    protected function getFilterRules(): array
    {
        // TODO: placeholder so that filter is passed by form request as validated to DTO
        return [
            FilterParser::param() => [
                'nullable',
            ],
        ];
    }

    /**
     * Get include validation rules.
     *
     * @return array
     */
    protected function getIncludeRules(): array
    {
        $schema = $this->schema();

        if (collect($schema->allowedIncludes())->isEmpty()) {
            return $this->prohibit(IncludeParser::param());
        }

        return $this->restrictAllowedIncludeValues(IncludeParser::param(), $schema);
    }

    /**
     * Restrict the allowed values for the schema includes.
     *
     * @param  string  $param
     * @param  Schema  $schema
     * @return array[]
     */
    protected function restrictAllowedIncludeValues(string $param, Schema $schema): array
    {
        return $this->restrictAllowedValues(
            $param,
            collect($schema->allowedIncludes())->map(fn (AllowedInclude $include) => $include->path())
        );
    }

    /**
     * Get allowed sorts for schema.
     *
     * @param  Schema  $schema
     * @return Collection
     */
    protected function formatAllowedSortValues(Schema $schema): Collection
    {
        $allowedSorts = collect();

        foreach ($schema->sorts() as $sort) {
            foreach (Direction::getInstances() as $direction) {
                $formattedSort = $sort->format($direction);
                if (! $allowedSorts->contains($formattedSort)) {
                    $allowedSorts->push($formattedSort);
                }
            }
        }

        return $allowedSorts;
    }

    /**
     * Restrict allowed sorts for schema.
     *
     * @param  string  $param
     * @param  Schema  $schema
     * @return array[]
     */
    protected function restrictAllowedSortValues(string $param, Schema $schema): array
    {
        return $this->restrictAllowedValues(
            $param,
            $this->formatAllowedSortValues($schema),
            [new DistinctIgnoringDirectionRule(), new RandomSoleRule()]
        );
    }

    /**
     * Get the paging validation rules.
     *
     * @return array
     */
    abstract protected function getPagingRules(): array;

    /**
     * Get the search validation rules.
     *
     * @return array
     */
    abstract protected function getSearchRules(): array;

    /**
     * Get the sort validation rules.
     *
     * @return array
     */
    abstract protected function getSortRules(): array;

    /**
     * Get the schema.
     *
     * @return Schema
     */
    abstract protected function schema(): Schema;

    /**
     * Get the validation API Query.
     *
     * @return Query
     */
    abstract public function getQuery(): Query;
}
