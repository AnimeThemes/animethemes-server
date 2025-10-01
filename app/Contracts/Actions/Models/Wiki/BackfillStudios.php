<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Models\Wiki;

interface BackfillStudios
{
    /**
     * Get the mapped studios.
     */
    public function getStudios(): array;
}
