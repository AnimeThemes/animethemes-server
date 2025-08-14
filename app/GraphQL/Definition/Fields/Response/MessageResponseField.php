<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Response;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Definition\Fields\Field;
use GraphQL\Type\Definition\Type;

class MessageResponseField extends Field implements DisplayableField
{
    public function __construct()
    {
        parent::__construct('message', nullable: false);
    }

    /**
     * The type returned by the field.
     */
    public function baseType(): Type
    {
        return Type::string();
    }

    /**
     * Determine if the field should be displayed to the user.
     */
    public function canBeDisplayed(): bool
    {
        return true;
    }
}
