<?php

declare(strict_types=1);

namespace App\Nova\Actions\Models\Wiki\Video;

use App\Actions\Models\Wiki\Video\Audio\BackfillAudioAction as BackfillAudio;
use App\Enums\Actions\Models\Wiki\Video\DeriveSourceVideo;
use App\Enums\Actions\Models\Wiki\Video\OverwriteAudio;
use App\Models\Auth\User;
use App\Models\Wiki\Video;
use App\Nova\Resources\Wiki\Video as VideoResource;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Enum;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Notifications\NovaNotification;

/**
 * Class BackfillAudioAction.
 */
class BackfillAudioAction extends Action implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    final public const DERIVE_SOURCE_VIDEO = 'derive_source_video';
    final public const OVERWRITE_AUDIO = 'overwrite_audio';

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
        return __('nova.actions.video.backfill.name');
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

        $deriveSourceVideo = DeriveSourceVideo::from(intval($fields->get(self::DERIVE_SOURCE_VIDEO)));
        $overwriteAudio = OverwriteAudio::from(intval($fields->get(self::OVERWRITE_AUDIO)));

        foreach ($models as $video) {
            $action = new BackfillAudio($video, $deriveSourceVideo, $overwriteAudio);

            try {
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
            } catch (Exception $e) {
                $this->markAsFailed($video, $e);
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
        return [
            Select::make(__('nova.actions.video.backfill.fields.derive_source.name'), self::DERIVE_SOURCE_VIDEO)
                ->options(DeriveSourceVideo::asSelectArray())
                ->displayUsing(fn (?int $enumValue) => DeriveSourceVideo::tryFrom($enumValue)?->localize())
                ->rules(['required', new Enum(DeriveSourceVideo::class)])
                ->default(DeriveSourceVideo::YES)
                ->help(__('nova.actions.video.backfill.fields.derive_source.help')),

            Select::make(__('nova.actions.video.backfill.fields.overwrite.name'), self::OVERWRITE_AUDIO)
                ->options(OverwriteAudio::asSelectArray())
                ->displayUsing(fn (?int $enumValue) => OverwriteAudio::tryFrom($enumValue)?->localize())
                ->rules(['required', new Enum(OverwriteAudio::class)])
                ->default(OverwriteAudio::NO)
                ->help(__('nova.actions.video.backfill.fields.overwrite.help')),
        ];
    }
}
