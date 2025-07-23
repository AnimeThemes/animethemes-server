<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Response;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Definition\Fields\Field;
use GraphQL\Type\Definition\Type;

/**
 * Class MessageResponseField.
 */
class MessageResponseField extends Field implements DisplayableField
{
    /**
     * Create a new Field instance.
     */
    public function __construct()
    {
        parent::__construct('message');
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    public function type(): Type
    {
        return Type::string();
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
