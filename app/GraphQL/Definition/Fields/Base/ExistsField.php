<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Base;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Resolvers\ExistsResolver;
use GraphQL\Type\Definition\Type;

/**
 * Class ExistsField.
 */
class ExistsField extends Field implements DisplayableField
{
    /**
     * Create a new Field instance.
     *
     * @param  string  $relation
     * @param  string  $column
     * @param  string|null  $name
     * @param  bool  $nullable
     */
    public function __construct(
        protected string $relation,
        protected string $column,
        protected ?string $name = null,
        protected bool $nullable = false,
    ) {
        parent::__construct($column, $name, $nullable);
    }

    /**
     * Get the directives of the field.
     *
     * @return array
     */
    public function directives(): array
    {
        return [
            'with' => [
                'relation' => $this->relation,
            ],
            'field' => [
                'resolver' => ExistsResolver::class,
            ],
        ];
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    public function type(): Type
    {
        return Type::boolean();
    }

    /**
     * Determine if the field should be displayed to the user.
     *
     * @return bool
     */
    public function canBeDisplayed(): bool
    {
        return true;
    }
}
