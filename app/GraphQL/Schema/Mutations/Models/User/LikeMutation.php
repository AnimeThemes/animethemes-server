<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Models\User;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\GraphQL\Controllers\User\LikeController;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Mutations\BaseMutation;
use App\GraphQL\Schema\Types\User\LikeType;
use App\GraphQL\Schema\Unions\LikedUnion;
use App\GraphQL\Support\Argument\Argument;
use App\Models\User\Like;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\App;

class LikeMutation extends BaseMutation
{
    public function __construct()
    {
        parent::__construct('like');
    }

    public function description(): string
    {
        return 'Like a model';
    }

    /**
     * Get the arguments for the like mutation.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        $type = new LikeType();

        return $this->resolveBindArguments($type->fieldClasses(), false);
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array<string, array>
     */
    public function rulesForValidation(array $args = []): array
    {
        $type = new LikeType();

        return collect($type->fieldClasses())
            ->filter(fn (Field $field) => $field instanceof CreatableField)
            ->mapWithKeys(fn (Field&CreatableField $field) => [$field->getColumn() => $field->getCreationRules($args)])
            ->toArray();
    }

    /**
     * The base return type of the mutation.
     */
    public function baseType(): LikedUnion
    {
        return new LikedUnion();
    }

    public function type(): Type
    {
        return Type::nonNull($this->toType());
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return App::make(LikeController::class)
            ->store($root, $args);
    }
}
