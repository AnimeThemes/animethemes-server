<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Concerns\Models\CanCreateExternalResource;
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
     * Create a new action instance.
     *
     * @param  BaseModel  $model
     * @param  array  $fields
     * @param  ResourceSite[]  $sites
     */
    public function __construct(protected BaseModel $model, protected array $fields, protected array $sites)
    {
    }

    /**
     * Handle the action.
     *
     * @return void
     */
    public function handle(): void
    {
        foreach ($this->sites as $resourceSite) {
            $link = Arr::get($this->fields, $resourceSite->name);

            $this->createResource($link, $resourceSite, $this->model);
        }
    }
}
