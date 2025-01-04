<?php

declare(strict_types=1);

namespace App\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Interface HasResources.
 */
interface HasResources
{
    /**
     * Get name.
     *
     * @return BelongsToMany
     */
    public function resources(): BelongsToMany;
}
