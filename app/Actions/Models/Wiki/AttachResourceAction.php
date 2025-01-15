<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Concerns\Models\CanCreateExternalResource;
use App\Contracts\Models\HasResources;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\BaseModel;
use Illuminate\Support\Arr;

/**
 * Class AttachResourceAction.
 */
class AttachResourceAction
{
    use CanCreateExternalResource;

    /**
     * Handle the action.
     *
     * @param  BaseModel&HasResources  $model
     * @param  array  $fields
     * @param  ResourceSite[]  $sites
     * @return void
     */
    public function handle(BaseModel&HasResources $model, array $fields, array $sites): void
    {
        foreach ($sites as $resourceSite) {
            $link = Arr::get($fields, $resourceSite->name);

            if (empty($link)) continue;

            $this->createResource($link, $resourceSite, $model);
        }
    }
}
