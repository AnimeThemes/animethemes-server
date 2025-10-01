<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Models\List\ExternalProfile;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Features\AllowExternalProfileManagement;
use App\GraphQL\Controllers\List\SyncExternalProfileController;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Mutations\BaseMutation;
use App\GraphQL\Schema\Types\List\ExternalProfileType;
use App\GraphQL\Support\Argument\Argument;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class SyncExternalProfileMutation extends BaseMutation
{
    protected $middleware = [
        EnabledOnlyOnLocalhost::class,
    ];

    public function __construct()
    {
        $this->middleware = array_merge(
            $this->middleware,
            [
                Str::of(EnsureFeaturesAreActive::class)
                    ->append(':')
                    ->append(AllowExternalProfileManagement::class)
                    ->__toString(),
            ]
        );

        parent::__construct('SyncExternalProfile');
    }

    public function description(): string
    {
        return 'Sync an external profile';
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
            ->mapWithKeys(fn (Field&BindableField $field): array => [$field->getColumn() => ['required']])
            ->toArray();
    }

    /**
     * The base return type of the mutation.
     */
    public function toType(): Type
    {
        return Type::string();
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
        return App::make(SyncExternalProfileController::class)
            ->store($root, $args);
    }
}
