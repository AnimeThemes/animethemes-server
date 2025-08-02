<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types;

use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Response\MessageResponseField;

class MessageResponseType extends BaseType
{
    /**
     * The description of the type.
     */
    public function getDescription(): string
    {
        return 'Represents a response containing a message.';
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            new MessageResponseField(),
        ];
    }
}
