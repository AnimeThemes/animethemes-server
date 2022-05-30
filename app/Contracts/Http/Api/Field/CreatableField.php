<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Field;

use Illuminate\Http\Request;

/**
 * Interface CreatableField.
 */
interface CreatableField
{
    /**
     * Set the creation validation rules for the field.
     *
     * @param  Request  $request
     * @return array
     */
    public function getCreationRules(Request $request): array;
}
