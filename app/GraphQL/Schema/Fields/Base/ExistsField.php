<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Base;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Schema\Fields\Field;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;

class ExistsField extends Field implements DisplayableField
{
    public function __construct(
        protected string $relation,
        protected ?string $name = null,
        protected bool $nullable = false,
    ) {
        parent::__construct($relation.'Exists', $name, $nullable);
    }

    /**
     * The type returned by the field.
     */
    public function baseType(): Type
    {
        return Type::boolean();
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }

    /**
     * Resolve the field.
     *
     * @param  Model  $root
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return $root->{$this->relation}->isNotEmpty();
    }
}
