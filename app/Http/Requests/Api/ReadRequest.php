<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Concerns\Http\Requests\Api\ValidatesConditionally;
use App\Concerns\Http\Requests\Api\ValidatesFields;
use App\Concerns\Http\Requests\Api\ValidatesFilters;
use App\Concerns\Http\Requests\Api\ValidatesIncludes;
use App\Concerns\Http\Requests\Api\ValidatesPaging;
use App\Concerns\Http\Requests\Api\ValidatesSorts;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\IncludeParser;
use Illuminate\Support\Arr;

abstract class ReadRequest extends BaseRequest
{
    use ValidatesConditionally;
    use ValidatesFields;
    use ValidatesFilters;
    use ValidatesIncludes;
    use ValidatesPaging;
    use ValidatesSorts;

    /**
     * Get the validation rules that apply to the request.
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
     */
    protected function getFieldRules(): array
    {
        $schema = $this->schema();
        $types = Arr::wrap($schema->type());
        $rules = $this->restrictAllowedFieldValues($schema);

        foreach ($schema->allowedIncludes() as $allowedInclude) {
            $relationSchema = $allowedInclude->schema();
            $types[] = $relationSchema->type();
            $rules += $this->restrictAllowedFieldValues($relationSchema);
        }

        return $rules + $this->restrictAllowedTypes(FieldParser::param(), $types);
    }

    /**
     * Get the filter validation rules.
     */
    abstract protected function getFilterRules(): array;

    /**
     * Get include validation rules.
     */
    protected function getIncludeRules(): array
    {
        $schema = $this->schema();

        if (empty($schema->allowedIncludes())) {
            return $this->prohibit(IncludeParser::param());
        }

        return $this->restrictAllowedIncludeValues(IncludeParser::param(), $schema);
    }

    /**
     * Get the paging validation rules.
     */
    abstract protected function getPagingRules(): array;

    /**
     * Get the search validation rules.
     */
    abstract protected function getSearchRules(): array;

    /**
     * Get the sort validation rules.
     */
    abstract protected function getSortRules(): array;
}
