<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;

class LocalizedEnumField extends Field implements DisplayableField
{
    public function __construct(
        protected EnumField $field,
    ) {
        parent::__construct($field->column, $field->getName().'Localized', $field->nullable);
    }

    public function description(): string
    {
        return "The formatted string value of the {$this->field->getName()} field";
    }

    public function baseType(): Type
    {
        return Type::string();
    }

    /**
     * Resolve the field.
     *
     * @param  mixed  $root
     */
    public function resolve(mixed $root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return Arr::get($root, $this->column)?->localize();
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }
}
