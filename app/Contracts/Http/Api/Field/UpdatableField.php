<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Field;

use Illuminate\Http\Request;

/**
 * Interface UpdatableField.
 */
interface UpdatableField
{
    /**
     * Set the update validation rules for the field.
     *
     * @param  Request  $request
     * @return array
     */
    public function getUpdateRules(Request $request): array;
}
