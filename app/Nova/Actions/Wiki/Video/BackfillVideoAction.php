<?php

declare(strict_types=1);

namespace App\Nova\Actions\Wiki\Video;

use App\Actions\Models\BaseAction;
use App\Actions\Models\Wiki\Video\Audio\BackfillVideoAudioAction;
use App\Models\Auth\User;
use App\Models\Wiki\Video;
use App\Nova\Resources\Wiki\Video as VideoResource;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Notifications\NovaNotification;

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
        $uriKey = VideoResource::uriKey();

        foreach ($models as $video) {
            $actions = $this->getActions($fields, $video);

            try {
                foreach ($actions as $action) {
                    $result = $action->handle();
                    if ($result->hasFailed()) {
                        $this->user->notify(
                            NovaNotification::make()
                                ->icon('flag')
                                ->message($result->getMessage())
                                ->type(NovaNotification::WARNING_TYPE)
                                ->url("/resources/$uriKey/{$video->getKey()}")
                        );
                    }
                }
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
     * Get the selected actions for backfilling video.
     *
     * @param  ActionFields  $fields
     * @param  Video  $video
     * @return BaseAction[]
     */
    protected function getActions(ActionFields $fields, Video $video): array
    {
        $actions = [];

        foreach ($this->getActionMapping($video) as $field => $action) {
            if (Arr::get($fields, $field) === true) {
                $actions[] = $action;
            }
        }

        return $actions;
    }

    /**
     * Get the mapping of actions to their form fields.
     *
     * @param  Video  $video
     * @return array<string, BaseAction>
     */
    protected function getActionMapping(Video $video): array
    {
        return [
            self::BACKFILL_AUDIO => new BackfillVideoAudioAction($video),
        ];
    }
}
