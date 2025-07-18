<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

/**
 * Interface DisplayableField.
 */
interface DisplayableField
{
    /**
     * Determine if the field should be displayed to the user.
     *
     * @return bool
     */
    public function canBeDisplayed(): bool;
}
