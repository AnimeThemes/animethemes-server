<?php

declare(strict_types=1);

namespace App\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Interface HasImages.
 */
interface HasImages
{
    /**
     * Get name.
     *
     * @return BelongsToMany
     */
    public function images(): BelongsToMany;
}
