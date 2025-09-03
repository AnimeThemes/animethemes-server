<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types;

use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Response\MessageResponseField;

class MessageResponseType extends BaseType
{
    public function description(): string
    {
        return 'Represents a response containing a message.';
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new MessageResponseField(),
        ];
    }
}
