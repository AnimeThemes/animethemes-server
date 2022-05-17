<?php

declare(strict_types=1);

namespace App\Concerns\Http\Requests\Api;

use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Schema\Schema;
use Illuminate\Support\Str;

/**
 * Trait ValidatesFields.
 */
trait ValidatesFields
{
    use ValidatesParameters;

    /**
     * Restrict the allowed values for the schema fields.
     *
     * @param  Schema  $schema
     * @return array<string, array>
     */
    protected function restrictAllowedFieldValues(Schema $schema): array
    {
        return $this->restrictAllowedValues(
            Str::of(FieldParser::param())->append('.')->append($schema->type())->__toString(),
            collect($schema->fields())->map(fn (Field $field) => $field->getKey())
        );
    }
}
