<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Models\List\ExternalProfile;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Exceptions\GraphQL\ClientForbiddenException;
use App\Features\AllowExternalProfileManagement;
use App\GraphQL\Argument\Argument;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Mutations\BaseMutation;
use App\GraphQL\Schema\Types\List\ExternalProfileType;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;
use App\Models\List\ExternalProfile;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class SyncExternalProfileMutation extends BaseMutation
{
    public function name(): string
    {
        return 'SyncExternalProfile';
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
            ->mapWithKeys(fn (Field&BindableField $field): array => [$field->name() => ['required']])
            ->all();
    }

    public function type(): Type
    {
        return Type::nonNull(Type::string());
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array<string, string>
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        $this->runHttpMiddlewares([
            EnabledOnlyOnLocalhost::class,
            EnsureFeaturesAreActive::using(AllowExternalProfileManagement::class),
        ]);

        /** @var ExternalProfile $profile */
        $profile = Arr::pull($args, 'model');

        throw_unless(
            $profile->canBeSynced(),
            ClientForbiddenException::class,
            'This external profile cannot be synced at the moment.'
        );

        $profile->dispatchSyncJob();

        return [
            'message' => 'Job dispatched.',
        ];
    }
}
