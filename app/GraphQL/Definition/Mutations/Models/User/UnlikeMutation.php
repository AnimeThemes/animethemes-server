<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Models\User;

use App\Contracts\GraphQL\Fields\DeletableField;
use App\GraphQL\Controllers\User\LikeController;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Mutations\BaseMutation;
use App\GraphQL\Definition\Types\User\LikeType;
use App\GraphQL\Definition\Unions\LikedUnion;
use App\GraphQL\Support\Argument\Argument;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\App;

class UnlikeMutation extends BaseMutation
{
    public function __construct()
    {
        parent::__construct('unlike');
    }

    /**
     * The description of the mutation.
     */
    public function description(): string
    {
        return 'Unlike a model';
    }

    /**
     * Get the arguments for the unlike mutation.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        $type = new LikeType();

        return $this->resolveBindArguments($type->fields(), false);
    }

    /**
     * Get the rules for the create mutation.
     *
     * @param  array<string, mixed>  $args
     * @return array<string, array>
     */
    protected function rules(array $args = []): array
    {
        $type = new LikeType();

        return collect($type->fields())
            ->filter(fn (Field $field) => $field instanceof DeletableField)
            ->mapWithKeys(fn (Field&DeletableField $field) => [$field->getColumn() => $field->getDeleteRules($args)])
            ->toArray();
    }

    /**
     * The base return type of the mutation.
     */
    public function baseRebingType(): LikedUnion
    {
        return new LikedUnion();
    }

    /**
     * The type returned by the field.
     */
    public function type(): Type
    {
        return Type::nonNull($this->baseType());
    }

    /**
     * Resolve the mutation.
     *
     * @param  array<string, mixed>  $args
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return App::make(LikeController::class)
            ->destroy($root, $args, $context, $resolveInfo);
    }
}
