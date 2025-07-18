<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;

/**
 * Class JsonField.
 */
abstract class JsonField extends Field implements DisplayableField
{
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
