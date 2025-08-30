<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Field;

use Illuminate\Http\Request;

interface CreatableField
{
    /**
     * @return array
     */
    public function getCreationRules(Request $request): array;
}
