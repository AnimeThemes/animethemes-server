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
    public function __construct(
        protected EloquentType $nodeType,
    ) {
        parent::__construct('node', nullable: false);
    }

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
