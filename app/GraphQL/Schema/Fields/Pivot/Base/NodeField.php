<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Pivot\Base;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\EloquentType;
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

    public function canBeDisplayed(): bool
    {
        return true;
    }
}
