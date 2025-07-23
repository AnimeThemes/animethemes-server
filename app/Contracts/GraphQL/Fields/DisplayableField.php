<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

interface DisplayableField
{
    /**
     * Determine if the field should be displayed to the user.
     */
    public function canBeDisplayed(): bool;
}
