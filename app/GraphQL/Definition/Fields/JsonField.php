<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;

abstract class JsonField extends Field implements DisplayableField
{
    public function canBeDisplayed(): bool
    {
        return true;
    }
}
