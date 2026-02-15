<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Models\List\ExternalProfile;

use App\Contracts\GraphQL\Fields\BindableField;
use App\GraphQL\Argument\Argument;
use App\GraphQL\Resolvers\List\SyncExternalProfileResolver;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Mutations\BaseMutation;
use App\GraphQL\Schema\Types\List\ExternalProfileType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\App;

class SyncExternalProfileMutation extends BaseMutation
{
    public function __construct()
    {
        parent::__construct('SyncExternalProfile');
    }

    public function description(): string
    {
        return 'Sync the entries of an external profile with an external tracking site.';
    }

    /**
     * Get the arguments for the create mutation.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        $type = new ExternalProfileType();

        return $this->resolveBindArguments($type->fieldClasses());
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array<string, array>
     */
    protected function rules(array $args = []): array
    {
        $type = new ExternalProfileType();

        return collect($type->fieldClasses())
            ->filter(fn (Field $field): bool => $field instanceof BindableField)
            ->mapWithKeys(fn (Field&BindableField $field): array => [$field->getName() => ['required']])
            ->all();
    }

    public function type(): Type
    {
        return Type::nonNull(Type::string());
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return App::make(SyncExternalProfileResolver::class)
            ->store($root, $args);
    }
}
