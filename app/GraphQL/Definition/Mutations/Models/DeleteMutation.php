<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Models;

use App\GraphQL\Definition\Mutations\BaseMutation;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Support\Argument\Argument;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class DeleteMutation extends BaseMutation
{
    /**
     * @param  class-string<Model>  $model
     */
    public function __construct(protected string $model)
    {
        parent::__construct('Delete'.Str::pascal(class_basename($model)));
    }

    /**
     * Get the arguments for the create mutation.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        $arguments = [];

        $baseType = $this->baseRebingType();

        if ($baseType instanceof BaseType) {
            $arguments[] = $this->resolveBindArguments($baseType->fieldClasses());
        }

        return Arr::flatten($arguments);
    }

    /**
     * The type returned by the field.
     */
    public function type(): Type
    {
        return Type::nonNull($this->baseType());
    }
}
