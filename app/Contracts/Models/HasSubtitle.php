<?php

declare(strict_types=1);

namespace App\Contracts\Models;

/**
 * Interface HasSubtitle.
 */
interface HasSubtitle
{
    /**
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle(): string;
}
