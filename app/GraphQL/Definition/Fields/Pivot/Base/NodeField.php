<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Pivot\Base;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\EloquentType;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Facades\GraphQL;

class NodeField extends Field implements DisplayableField
{
    /**
     * @param  class-string<EloquentType>  $nodeType
     */
    public function __construct(
        protected string $nodeType,
    ) {
        parent::__construct('node', nullable: false);
    }

    /**
     * The type returned by the field.
     */
    public function baseType(): Type
    {
        $type = Str::of(class_basename($this->nodeType))
            ->remove('Type')
            ->__toString();

        return Type::nonNull(GraphQL::type($type));
    }

    /**
     * Determine if the field should be displayed to the user.
     */
    public function canBeDisplayed(): bool
    {
        return true;
    }
}
