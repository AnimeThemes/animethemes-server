<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Models\Wiki;

interface BackfillStudios
{
    /**
     * Get the mapped studios.
     *
     * @return array
     */
    public function getStudios(): array;
}
