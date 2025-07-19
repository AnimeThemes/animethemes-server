<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\List;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Features\AllowExternalProfileManagement;
use App\GraphQL\Attributes\UseField;
use App\GraphQL\Controllers\List\SyncExternalProfileController;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Mutations\BaseMutation;
use App\GraphQL\Definition\Types\List\ExternalProfileType;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;
use App\Models\List\ExternalProfile;
use GraphQL\Type\Definition\Type;

/**
 * Class SyncExternalProfileMutation.
 */
#[UseField(SyncExternalProfileController::class, 'store')]
class SyncExternalProfileMutation extends BaseMutation
{
    /**
     * Create a new mutation instance.
     */
    public function __construct()
    {
        parent::__construct('syncExternalProfile');
    }

    /**
     * The description of the mutation.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Sync an external profile';
    }

    /**
     * Get the arguments for the create mutation.
     *
     * @return string[]
     */
    public function arguments(): array
    {
        $type = new ExternalProfileType();

        return $this->resolveBindArgument($type->fields());
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
     *
     * @return Type
     */
    public function baseType(): Type
    {
        return Type::string();
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    public function getType(): Type
    {
        return Type::nonNull($this->baseType());
    }
}
