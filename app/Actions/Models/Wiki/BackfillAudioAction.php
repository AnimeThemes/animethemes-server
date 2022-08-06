<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Actions\Models\ActionResult;
use App\Actions\Models\BaseAction;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Audio;
use Illuminate\Support\Facades\Log;

/**
 * Class BackfillAudioAction.
 *
 * @template TModel of \App\Models\BaseModel
 * @extends BaseAction<TModel>
 */
abstract class BackfillAudioAction extends BaseAction
{
    /**
     * Handle action.
     *
     * @return ActionResult
     */
    public function handle(): ActionResult
    {
        if ($this->relation()->getQuery()->exists()) {
            Log::info("{$this->label()} '{$this->getModel()->getName()}' already has Audio'.");

            return new ActionResult(ActionStatus::SKIPPED());
        }

        $audio = $this->getAudio();

        if ($audio !== null) {
            $this->attachAudio($audio);
        }

        if ($this->relation()->getQuery()->doesntExist()) {
            return new ActionResult(
                ActionStatus::FAILED(),
                "{$this->label()} '{$this->getModel()->getName()}' has no Audio after backfilling. Please review."
            );
        }

        return new ActionResult(ActionStatus::PASSED());
    }

    /**
     * Get or Create Audio.
     *
     * @return Audio|null
     */
    abstract protected function getAudio(): ?Audio;

    /**
     * Attach Audio to model.
     *
     * @param  Audio  $audio
     * @return void
     */
    abstract protected function attachAudio(Audio $audio): void;
}
