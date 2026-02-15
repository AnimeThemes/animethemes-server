<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Models;

use App\GraphQL\Argument\Argument;
use App\GraphQL\Schema\Mutations\BaseMutation;
use App\GraphQL\Schema\Types\BaseType;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Facades\GraphQL;

abstract class DeleteMutation extends BaseMutation
{
    /**
     * @param  class-string<Model>  $model
     */
    public function __construct(protected string $model)
    {
        parent::__construct('Delete'.Str::pascal(class_basename($model)));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $model = Arr::pull($args, 'model');

        $args = collect($args)
            ->filter(fn ($value): bool => $value instanceof Model)
            ->prepend($model)
            ->values()
            ->all();

        return ($this->response = Gate::inspect('delete', $args))->allowed();
    }

    /**
     * Get the arguments for the create mutation.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        $arguments = [];

        $baseType = $this->baseType();

        if ($baseType instanceof BaseType) {
            $arguments[] = $this->resolveBindArguments($baseType->fieldClasses());
        }

        return Arr::flatten($arguments);
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type($this->baseType()->getName()));
    }
}
