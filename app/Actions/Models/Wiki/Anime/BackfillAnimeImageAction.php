<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Anime;

use App\Actions\Models\Wiki\BackfillImageAction;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;

/**
 * Class BackfillAnimeImageAction.
 *
 * @extends BackfillImageAction<Anime>
 */
abstract class BackfillAnimeImageAction extends BackfillImageAction
{
    /**
     * Create a new action instance.
     *
     * @param  Anime  $anime
     */
    public function __construct(Anime $anime)
    {
        parent::__construct($anime);
    }

    /**
     * Get the model the action is handling.
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
