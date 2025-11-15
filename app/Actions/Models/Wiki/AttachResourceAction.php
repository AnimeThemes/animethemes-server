<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Actions\ActionResult;
use App\Concerns\Models\CanCreateExternalResource;
use App\Contracts\Models\HasResources;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\BaseModel;
use Illuminate\Support\Arr;

class AttachResourceAction
{
    use CanCreateExternalResource;

    /**
     * @param  ResourceSite[]  $sites
     */
    public function handle(BaseModel&HasResources $model, array $fields, array $sites): ActionResult
    {
        foreach ($sites as $resourceSite) {
            $url = Arr::get($fields, $resourceSite->name);

            if (blank($url)) {
                continue;
            }

            $this->createResource($url, $resourceSite, $model);
        }

        return new ActionResult(ActionStatus::PASSED);
    }
}
