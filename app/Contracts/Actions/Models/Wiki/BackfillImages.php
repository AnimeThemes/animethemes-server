<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Models\Wiki;

/**
 * Interface BackfillImages.
 */
interface BackfillImages
{
    /**
     * Get the mapping for the images.
     *
     * @return string[]
     */
    public function getImagesMapping(): array;
}
