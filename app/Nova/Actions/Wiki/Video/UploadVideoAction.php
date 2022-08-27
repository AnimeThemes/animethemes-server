<?php

declare(strict_types=1);

namespace App\Nova\Actions\Wiki\Video;

use App\Actions\Storage\Wiki\Video\UploadVideoAction as UploadVideo;
use App\Constants\Config\VideoConstants;
use App\Rules\Storage\StorageDirectoryExistsRule;
use App\Rules\Wiki\Submission\Audio\AudioChannelLayoutStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioChannelsStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioCodecStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioIndexStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioLoudnessIntegratedTargetStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioLoudnessTruePeakStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioSampleRateStreamRule;
use App\Rules\Wiki\Submission\Format\EncoderNameFormatRule;
use App\Rules\Wiki\Submission\Format\EncoderVersionFormatRule;
use App\Rules\Wiki\Submission\Format\ExtraneousChaptersFormatRule;
use App\Rules\Wiki\Submission\Format\ExtraneousMetadataFormatRule;
use App\Rules\Wiki\Submission\Format\FormatNameFormatRule;
use App\Rules\Wiki\Submission\Format\TotalStreamsFormatRule;
use App\Rules\Wiki\Submission\Format\VideoBitrateRestrictionFormatRule;
use App\Rules\Wiki\Submission\Video\VideoCodecStreamRule;
use App\Rules\Wiki\Submission\Video\VideoColorPrimariesStreamRule;
use App\Rules\Wiki\Submission\Video\VideoColorSpaceStreamRule;
use App\Rules\Wiki\Submission\Video\VideoColorTransferStreamRule;
use App\Rules\Wiki\Submission\Video\VideoIndexStreamRule;
use App\Rules\Wiki\Submission\Video\VideoPixelFormatStreamRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File as FileRule;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class UploadVideoAction.
 */
class UploadVideoAction extends Action
{
    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.upload_video');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return array
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function handle(ActionFields $fields, Collection $models): array
    {
        /** @var UploadedFile $file */
        $file = $fields->get('file');
        $path = $fields->get('path');

        $action = new UploadVideo($file, $path);

        $storageResults = $action->handle();

        $storageResults->toLog();

        $result = $storageResults->toActionResult();

        if ($result->hasFailed()) {
            return Action::danger($result->getMessage());
        }

        return Action::message($result->getMessage());
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
        $fs = Storage::disk(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        return [
            File::make(__('nova.video'), 'file')
                ->required()
                ->rules([
                    'required',
                    FileRule::types('webm')->max(200 * 1024),
                    new TotalStreamsFormatRule(2),
                    new EncoderNameFormatRule(),
                    new EncoderVersionFormatRule(),
                    new FormatNameFormatRule('matroska,webm'),
                    new VideoBitrateRestrictionFormatRule(),
                    new ExtraneousMetadataFormatRule(),
                    new ExtraneousChaptersFormatRule(),
                    new AudioIndexStreamRule(1),
                    new AudioCodecStreamRule(),
                    new AudioSampleRateStreamRule(),
                    new AudioChannelsStreamRule(),
                    new AudioChannelLayoutStreamRule(),
                    new AudioLoudnessTruePeakStreamRule(),
                    new AudioLoudnessIntegratedTargetStreamRule(),
                    new VideoIndexStreamRule(),
                    new VideoCodecStreamRule(),
                    new VideoPixelFormatStreamRule(),
                    new VideoColorSpaceStreamRule(),
                    new VideoColorTransferStreamRule(),
                    new VideoColorPrimariesStreamRule(),
                ])
                ->help(__('nova.upload_video_help')),

            Text::make(__('nova.path'), 'path')
                ->required()
                ->rules(['required', 'string', 'doesnt_start_with:/', 'doesnt_end_with:/', new StorageDirectoryExistsRule($fs)])
                ->help(__('nova.upload_video_path_help')),
        ];
    }
}
