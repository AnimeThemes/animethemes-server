<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Models\List\ExternalProfile;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Features\AllowExternalProfileManagement;
use App\GraphQL\Attributes\Resolvers\UseFieldDirective;
use App\GraphQL\Controllers\List\SyncExternalProfileController;
use App\GraphQL\Definition\Argument\Argument;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Mutations\BaseMutation;
use App\GraphQL\Definition\Types\List\ExternalProfileType;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;
use App\Models\List\ExternalProfile;
use GraphQL\Type\Definition\Type;

#[UseFieldDirective(SyncExternalProfileController::class, 'store')]
class SyncExternalProfileMutation extends BaseMutation
{
    public function __construct()
    {
        parent::__construct('syncExternalProfile');
    }

    /**
     * The description of the mutation.
     */
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

        return $this->resolveBindArguments($type->fields());
    }

    /**
     * The directives of the mutation.
     *
     * @return array<string, array>
     */
    public function directives(): array
    {
        return [
            'middleware' => [
                'class' => EnabledOnlyOnLocalhost::class,
            ],
            'featureEnabled' => [
                'class' => AllowExternalProfileManagement::class,
            ],
            'canModel' => [
                'ability' => 'update',
                'injectArgs' => true,
                'model' => ExternalProfile::class,
            ],
            ...parent::directives(),
        ];
    }

    /**
     * Get the rules for the create mutation.
     *
     * @param  array<string, mixed>  $args
     * @return array<string, array>
     */
    public function rules(array $args): array
    {
        $type = new ExternalProfileType();

        return collect($type->fields())
            ->filter(fn (Field $field) => $field instanceof BindableField)
            ->mapWithKeys(fn (Field&BindableField $field) => [$field->getColumn() => ['required']])
            ->toArray();
    }

    /**
     * The base return type of the mutation.
     */
    public function baseType(): Type
    {
        return Type::string();
    }

    /**
     * The type returned by the field.
     */
    public function getType(): Type
    {
        return Type::nonNull($this->baseType());
    }
}
