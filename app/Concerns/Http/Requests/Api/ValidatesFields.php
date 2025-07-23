<?php

declare(strict_types=1);

namespace App\Concerns\Http\Requests\Api;

use App\Contracts\Http\Api\Field\FieldInterface;
use App\Contracts\Http\Api\Schema\SchemaInterface;
use App\Http\Api\Parser\FieldParser;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait ValidatesFields
{
    use ValidatesParameters;

    /**
     * Restrict the allowed values for the schema fields.
     *
     * @return array<string, array>
     */
    protected function restrictAllowedFieldValues(SchemaInterface $schema): array
    {
        return $this->restrictAllowedValues(
            Str::of(FieldParser::param())->append('.')->append($schema->type())->__toString(),
            Arr::map($schema->fields(), fn (FieldInterface $field) => $field->getKey())
        );
    }
}
