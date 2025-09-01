<?php

declare(strict_types=1);

namespace App\Contracts\Models;

use App\Models\Wiki\Image;
use App\Pivots\Morph\Imageable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface HasImages
{
    public const IMAGES_RELATION = 'images';

    /**
     * Get the images for the owner model.
     *
     * @return MorphToMany<Image, Model&HasImages, Imageable>
     */
    public function images(): MorphToMany;
}
