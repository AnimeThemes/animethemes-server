<?php

declare(strict_types=1);

namespace App\Nova\Actions\Wiki\Video;

use App\Models\Auth\User;
use App\Models\Wiki\Video;
use App\Pipes\BasePipe;
use App\Pipes\Wiki\Video\BackfillAudio;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class BackfillVideoAction.
 */
class BackfillVideoAction extends Action implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    final public const BACKFILL_AUDIO = 'backfill_audio';

    /**
     * Create a new action instance.
     *
     * @param  User  $user
     */
    public function __construct(protected User $user)
    {
    }

    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.backfill_video');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection<int, Video>  $models
     * @return Collection
     */
    public function handle(ActionFields $fields, Collection $models): Collection
    {
        foreach ($models as $video) {
            $pipes = $this->getPipes($fields, $video);

            $pipeline = new Pipeline(Container::getInstance());

            try {
                $pipeline->send($this->user)
                    ->through($pipes)
                    ->then(fn () => $this->markAsFinished($video));
            } catch (Exception $e) {
                $this->markAsFailed($video, $e);
            } finally {
                // Try not to upset third-party APIs
                sleep(rand(3, 5));
            }
        }

        return $models;
    }

    /**
     * Get the fields available on the action.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(NovaRequest $request): array
    {
        $video = $request->resourceId !== null
            ? $request->findModel()
            : null;

        return [
            Heading::make(__('nova.audio')),

            Boolean::make(__('nova.backfill_audio'), self::BACKFILL_AUDIO)
                ->help(__('nova.backfill_audio_help'))
                ->default(fn () => $video instanceof Video && $video->audio()->doesntExist()),
        ];
    }

    /**
     * Get the selected pipes for backfilling anime.
     *
     * @param  ActionFields  $fields
     * @param  Video  $video
     * @return BasePipe[]
     */
    protected function getPipes(ActionFields $fields, Video $video): array
    {
        $pipes = [];

        foreach ($this->getPipeMapping($video) as $field => $pipe) {
            if (Arr::get($fields, $field) === true) {
                $pipes[] = $pipe;
            }
        }

        return $pipes;
    }

    /**
     * Get the mapping of anime pipes to their form fields.
     *
     * @param  Video  $video
     * @return array<string, BasePipe>
     */
    protected function getPipeMapping(Video $video): array
    {
        return [
            self::BACKFILL_AUDIO => new BackfillAudio($video),
        ];
    }
}
