<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Response;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Schema\Fields\Field;
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

    public function canBeDisplayed(): bool
    {
        return true;
    }
}
