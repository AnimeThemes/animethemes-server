<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Studio;

use App\Actions\Models\Wiki\BackfillImageAction;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;

/**
 * Class BackfillStudioImageAction.
 *
 * @extends BackfillImageAction<Studio>
 */
abstract class BackfillStudioImageAction extends BackfillImageAction
{
    /**
     * Create a new action instance.
     *
     * @param  Studio  $studio
     */
    public function __construct(Studio $studio)
    {
        parent::__construct($studio);
    }

    /**
     * Get the model the action is handling.
     *
     * @return Studio
     */
    protected function getModel(): Studio
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
