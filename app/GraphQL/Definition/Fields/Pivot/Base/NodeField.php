<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Pivot\Base;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\EloquentType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;

class NodeField extends Field implements DisplayableField
{
    /**
     * Create a new field instance.
     *
     * @param  class-string<EloquentType>  $nodeType
     */
    public function __construct(
        protected string $nodeType,
    ) {
        parent::__construct('node', nullable: false);
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    public function type(): Type
    {
        $type = Str::of(class_basename($this->nodeType))
            ->remove('Type')
            ->__toString();

        // Necessary to prevent memory leak at compile time.
        return new ObjectType(['name' => $type, 'fields' => []]);
    }

    /**
     * Determine if the field should be displayed to the user.
     *
     * @return bool
     */
    public function canBeDisplayed(): bool
    {
        return true;
    }
}
