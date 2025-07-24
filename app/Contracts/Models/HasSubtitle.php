<?php

declare(strict_types=1);

namespace App\Contracts\Models;

interface HasSubtitle
{
    /**
     * Get subtitle.
     */
    public function getSubtitle(): string;
}
