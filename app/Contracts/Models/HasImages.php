<?php

declare(strict_types=1);

namespace App\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface HasImages
{
    public const IMAGES_RELATION = 'images';

    /**
     * Get the images for the owner model.
     *
     * @return MorphToMany
     */
    public function images(): MorphToMany;
}
