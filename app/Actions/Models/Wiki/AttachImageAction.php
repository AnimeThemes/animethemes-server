<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Actions\ActionResult;
use App\Concerns\Models\CanCreateImage;
use App\Contracts\Models\HasImages;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\ImageFacet;
use App\Models\BaseModel;
use Illuminate\Support\Arr;

class AttachImageAction
{
    use CanCreateImage;

    /**
     * @param  ImageFacet[]  $facets
     */
    public function handle(BaseModel&HasImages $model, array $fields, array $facets): ActionResult
    {
        foreach ($facets as $facet) {
            $image = Arr::get($fields, $facet->name);

            if (blank($image)) {
                continue;
            }

            $this->createImageFromFile($image, $facet, $model);
        }

        return new ActionResult(ActionStatus::PASSED);
    }
}
