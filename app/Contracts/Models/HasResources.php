<?php

declare(strict_types=1);

namespace App\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface HasResources
{
    /**
     * Get the resources for the owner model.
     *
     * @return MorphToMany
     */
    public function resources(): MorphToMany;
}
