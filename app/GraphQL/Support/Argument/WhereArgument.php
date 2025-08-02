<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Argument;

use App\Contracts\GraphQL\Fields\FilterableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\BaseType;
use Illuminate\Support\Str;

class WhereArgument extends Argument
{
    public function __construct(protected BaseType $type)
    {
        $enumName = static::buildEnumName($type);

        parent::__construct('where', $enumName);

        $this->directives([
            'whereConditions' => [
                'columnsEnum' => $enumName,
            ],
        ]);
    }

    /**
     * Build the enum that applies the where conditions query.
     */
    public static function buildEnum(BaseType $type): string
    {
        $filterableFields = collect($type->fields())
            ->filter(fn (Field $field) => $field instanceof FilterableField)
            ->map(
                fn (Field $field) => Str::of($field->getName())
                    ->snake()
                    ->upper()
                    ->append(static::resolveDirectives([
                        'enum' => [
                            'value' => $field->getColumn(),
                        ],
                    ]))
                    ->__toString()
            )
            ->implode(PHP_EOL);

        if (blank($filterableFields)) {
            return '';
        }

        return Str::of('enum ')
            ->append(static::buildEnumName($type))
            ->append(' {')
            ->newLine()
            ->append($filterableFields)
            ->newLine()
            ->append('}')
            ->__toString();
    }

    /**
     * Build the enum name for the columns.
     * Template: {TypeName}FilterableColumn.
     */
    protected static function buildEnumName(BaseType $type): string
    {
        return Str::of($type->getName())
            ->append('FilterableColumn')
            ->__toString();
    }
}
