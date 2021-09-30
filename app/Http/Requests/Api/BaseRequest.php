<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query;
use App\Http\Api\Schema\Schema;
use Illuminate\Foundation\Http\FormRequest;
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
     * Get the field validation rules.
     *
     * @return array
     */
    protected function getFieldRules(): array
    {
        $schema = $this->getSchema();

        $types = collect($schema->type());

        $rules = $this->getSchemaFieldRules($schema);

        foreach ($schema->allowedIncludes() as $allowedIncludePath) {
            $relationSchema = $allowedIncludePath->schema();

            $types->push($relationSchema->type());

            $rules = array_merge($rules, $this->getSchemaFieldRules($relationSchema));
        }

        return array_merge(
            $rules,
            [
                FieldParser::$param => [
                    'nullable',
                    Str::of('array:')->append($types->join(','))->__toString(),
                ],
            ],
        );
    }

    /**
     * Get the validation rules for the schema.
     *
     * @param  Schema  $schema
     * @return array
     */
    protected function getSchemaFieldRules(Schema $schema): array
    {
        return [
            Str::of(FieldParser::$param)
                ->append('.')
                ->append($schema->type())
                ->__toString() => [
                    'sometimes',
                    'required',
                    new Delimited(Rule::in(collect($schema->fields())->map(fn (Field $field) => $field->getKey()))),
                ],
        ];
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
            FilterParser::$param => [
                'nullable',
            ],
        ];
    }

    /**
     * Get the include validation rules.
     *
     * @return array
     */
    protected function getIncludeRules(): array
    {
        $schema = $this->getSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        if ($allowedIncludes->isEmpty()) {
            return [
                IncludeParser::$param => [
                    'prohibited',
                ],
            ];
        }

        return [
            IncludeParser::$param => [
                'sometimes',
                'required',
                new Delimited(Rule::in($allowedIncludes->map(fn (AllowedInclude $include) => $include->path()))),
            ],
        ];
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
    abstract protected function getSchema(): Schema;

    /**
     * Get the validated API Query.
     *
     * @return Query
     */
    public function getQuery(): Query
    {
        return Query::make($this->validated());
    }
}
