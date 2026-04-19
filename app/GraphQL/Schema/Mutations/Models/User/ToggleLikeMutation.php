<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Models\User;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\Models\Likeable;
use App\GraphQL\Argument\Argument;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Mutations\BaseMutation;
use App\GraphQL\Schema\Types\User\LikeType;
use App\Models\User\Like;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ToggleLikeMutation extends BaseMutation
{
    final public const string ATTRIBUTE_ENTRY = 'entry';
    final public const string ATTRIBUTE_PLAYLIST = 'playlist';

    public function name(): string
    {
        return 'ToggleLike';
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, $selectFields = null): bool
    {
        return ($this->response = Gate::inspect('create', [Like::class]))->allowed();
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
            ->filter(fn (Field $field): bool => $field instanceof CreatableField)
            ->mapWithKeys(fn (Field&CreatableField $field): array => [$field->name() => $field->getCreationRules($args)])
            ->all();
    }

    /**
     * The base return type of the mutation.
     */
    public function baseType(): LikeType
    {
        return new LikeType();
    }

    public function type(): Type
    {
        return GraphQL::type($this->baseType()->name());
    }

    /**
     * @param  array<string, mixed>  $args
     * @return Like|null
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        $validated = Validator::make($args, $this->rulesForValidation($args))->validated();

        /** @var Model&Likeable $likeable */
        $likeable = Arr::first($validated);

        return $likeable->toggleLike(Auth::user());
    }
}
