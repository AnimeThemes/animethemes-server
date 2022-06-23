<?php

declare(strict_types=1);

namespace App\Pipes\Wiki\Studio;

use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Nova\Resources\Resource;
use App\Nova\Resources\Wiki\Studio as StudioResource;
use App\Pipes\Wiki\BackfillImage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;

/**
 * Class BackfillAnimeImage.
 *
 * @extends BackfillImage<Studio>
 */
abstract class BackfillStudioImage extends BackfillImage
{
    /**
     * Create a new pipe instance.
     *
     * @param  Studio  $studio
     */
    public function __construct(Studio $studio)
    {
        parent::__construct($studio);
    }

    /**
     * Get the model passed into the pipeline.
     *
     * @return Studio
     */
    public function getModel(): Studio
    {
        return $this->model;
    }

    /**
     * Get the relation to images.
     *
     * @return BelongsToMany
     */
    protected function relation(): BelongsToMany
    {
        return $this->getModel()->images();
    }

    /**
     * Get the nova resource.
     *
     * @return class-string<Resource>
     */
    protected function resource(): string
    {
        return StudioResource::class;
    }

    /**
     * Attach Image to Studio.
     *
     * @param  Image  $image
     * @return void
     */
    protected function attachImage(Image $image): void
    {
        Log::info("Attaching Image '{$image->getName()}' to {$this->label()} '{$this->getModel()->getName()}'");
        $this->relation()->attach($image);
    }
}
