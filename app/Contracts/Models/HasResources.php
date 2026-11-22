<?php

declare(strict_types=1);

namespace App\Contracts\Models;

use App\Models\Wiki\ExternalResource;
use App\Pivots\Morph\Resourceable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface HasResources
{
    public const RESOURCES_RELATION = 'resources';

    /**
     * Get the resources for the owner model.
     *
     * @return MorphToMany<ExternalResource, Model&HasResources, Resourceable>
     */
    public function resources(): MorphToMany;

    /**
     * Get the resources for the owner model.
     *
     * @return MorphMany<ExternalResource, Model&HasResources>
     */
    public function resourcesV2(): MorphMany;
}
