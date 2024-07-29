<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Concerns\Models\CanCreateImage;
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
     * Create a new action instance.
     *
     * @param  BaseModel  $model
     * @param  array  $fields
     * @param  ImageFacet[]  $facets
     */
    public function __construct(protected BaseModel $model, protected array $fields, protected array $facets)
    {
    }

    /**
     * Perform the action on the given models.
     *
     * @return void
     */
    public function handle(): void
    {
        foreach ($this->facets as $facet) {
            $image = Arr::get($this->fields, $facet->name);

            if (empty($image)) continue;

            $this->createImageFromFile($image, $facet, $this->model);
        }
    }
}
