<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Field;

use Illuminate\Http\Request;

interface CreatableField
{
    /**
     * Set the creation validation rules for the field.
     *
     * @return array
     */
    public function getCreationRules(Request $request): array;
}
