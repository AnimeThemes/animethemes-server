<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Models\Wiki;

interface BackfillImages
{
    /**
     * Get the mapping for the images.
     *
     * @return string[]
     */
    public function getImagesMapping(): array;
}
