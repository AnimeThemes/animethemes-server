<?php

declare(strict_types=1);

namespace App\Pipes\Wiki\Video;

use App\Actions\Wiki\Video\BackfillAudio as BackfillAudioAction;
use App\Models\Auth\User;
use App\Models\Wiki\Video;
use App\Nova\Resources\BaseResource;
use App\Nova\Resources\Wiki\Video as VideoResource;
use App\Pipes\BasePipe;
use Closure;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class BackfillAudio.
 *
 * @extends BasePipe<Video>
 */
class BackfillAudio extends BasePipe
{
    /**
     * Create a new pipe instance.
     *
     * @param  Video  $video
     */
    public function __construct(Video $video)
    {
        parent::__construct($video);
    }

    /**
     * Handle an incoming request.
     *
     * @param  User  $user
     * @param  Closure(User): mixed  $next
     * @return mixed
     */
    public function handle(User $user, Closure $next): mixed
    {
        $action = new BackfillAudioAction();

        $action->backfill($this->getModel());

        if ($this->relation()->getQuery()->doesntExist()) {
            $this->sendNotification($user, "{$this->label()} '{$this->getModel()->getName()}' has no Audio after backfilling. Please review.");
        }

        return $next($user);
    }

    /**
     * Get the model passed into the pipeline.
     *
     * @return Video
     */
    public function getModel(): Video
    {
        return $this->model;
    }

    /**
     * Get the relation to resources.
     *
     * @return BelongsTo
     */
    protected function relation(): BelongsTo
    {
        return $this->getModel()->audio();
    }

    /**
     * Get the nova resource.
     *
     * @return class-string<BaseResource>
     */
    protected function resource(): string
    {
        return VideoResource::class;
    }
}
