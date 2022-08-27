<?php

declare(strict_types=1);

namespace App\Nova\Actions\Wiki\Video;

use App\Actions\Models\Wiki\Video\Audio\BackfillAudioAction as BackfillAudio;
use App\Enums\Actions\Models\Wiki\Video\DeriveSourceVideo;
use App\Enums\Actions\Models\Wiki\Video\OverwriteAudio;
use App\Models\Auth\User;
use App\Models\Wiki\Video;
use App\Nova\Resources\Wiki\Video as VideoResource;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
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
        return __('nova.backfill_audio');
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

        $deriveSourceVideo = DeriveSourceVideo::fromValue(intval($fields->get(self::DERIVE_SOURCE_VIDEO)));
        $overwriteAudio = OverwriteAudio::fromValue(intval($fields->get(self::OVERWRITE_AUDIO)));

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
            Select::make(__('nova.backfill_audio_derive_source'), self::DERIVE_SOURCE_VIDEO)
                ->options(DeriveSourceVideo::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->rules(['required', new EnumValue(DeriveSourceVideo::class, false)])
                ->default(DeriveSourceVideo::YES)
                ->help(__('nova.backfill_audio_derive_source_help')),

            Select::make(__('nova.backfill_audio_overwrite'), self::OVERWRITE_AUDIO)
                ->options(OverwriteAudio::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->rules(['required', new EnumValue(OverwriteAudio::class, false)])
                ->default(OverwriteAudio::NO)
                ->help(__('nova.backfill_audio_overwrite_help')),
        ];
    }
}
