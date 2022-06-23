<?php

declare(strict_types=1);

namespace App\Pipes\Wiki\Anime;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Nova\Resources\BaseResource;
use App\Nova\Resources\Wiki\Anime as AnimeResource;
use App\Pipes\Wiki\BackfillImage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;

/**
 * Class BackfillAnimeImage.
 *
 * @extends BackfillImage<Anime>
 */
abstract class BackfillAnimeImage extends BackfillImage
{
    /**
     * Create a new pipe instance.
     *
     * @param  Anime  $anime
     */
    public function __construct(Anime $anime)
    {
        parent::__construct($anime);
    }

    /**
     * Get the model passed into the pipeline.
     *
     * @return Anime
     */
    public function getModel(): Anime
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
     * @return class-string<BaseResource>
     */
    protected function resource(): string
    {
        return AnimeResource::class;
    }

    /**
     * Attach Image to Anime.
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
