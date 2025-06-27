<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Concerns\Models\CanCreateImage;
use App\Contracts\Models\HasImages;
use App\Enums\Models\Wiki\ImageFacet;
use App\Models\BaseModel;
use Illuminate\Support\Arr;

/**
 * Class AttachImageAction.
 */
class AttachImageAction
{
    use CanCreateImage;

    /**
     * Perform the action on the given models.
     *
     * @param  BaseModel&HasImages  $model
     * @param  array  $fields
     * @param  ImageFacet[]  $facets
     * @return void
     */
    public function handle(BaseModel&HasImages $model, array $fields, array $facets): void
    {
        foreach ($facets as $facet) {
            $image = Arr::get($fields, $facet->name);

            if (empty($image)) {
                continue;
            }

            $this->createImageFromFile($image, $facet, $model);
        }
    }
}
