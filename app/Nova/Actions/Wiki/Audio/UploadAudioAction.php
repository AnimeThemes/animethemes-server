<?php

declare(strict_types=1);

namespace App\Nova\Actions\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\UploadAudioAction as UploadAudio;
use App\Constants\Config\AudioConstants;
use App\Rules\Wiki\StorageDirectoryExistsRule;
use App\Rules\Wiki\Submission\Audio\AudioChannelLayoutStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioChannelsStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioCodecStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioIndexStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioLoudnessIntegratedTargetStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioLoudnessTruePeakStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioSampleRateStreamRule;
use App\Rules\Wiki\Submission\Format\AudioBitrateRestrictionFormatRule;
use App\Rules\Wiki\Submission\Format\EncoderNameFormatRule;
use App\Rules\Wiki\Submission\Format\EncoderVersionFormatRule;
use App\Rules\Wiki\Submission\Format\ExtraneousChaptersFormatRule;
use App\Rules\Wiki\Submission\Format\ExtraneousMetadataFormatRule;
use App\Rules\Wiki\Submission\Format\FormatNameFormatRule;
use App\Rules\Wiki\Submission\Format\TotalStreamsFormatRule;
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
 * Class UploadAudioAction.
 */
class UploadAudioAction extends Action
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
        return __('nova.upload_audio');
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

        $action = new UploadAudio($file, $path);

        $result = $action->handle();

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
        $fs = Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        return [
            File::make(__('nova.audio'), 'file')
                ->required()
                ->rules([
                    'required',
                    FileRule::types('ogg')->max(200 * 1024),
                    new TotalStreamsFormatRule(1),
                    new EncoderNameFormatRule(),
                    new EncoderVersionFormatRule(),
                    new FormatNameFormatRule('ogg'),
                    new AudioBitrateRestrictionFormatRule(),
                    new ExtraneousMetadataFormatRule(),
                    new ExtraneousChaptersFormatRule(),
                    new AudioIndexStreamRule(0),
                    new AudioCodecStreamRule(),
                    new AudioSampleRateStreamRule(),
                    new AudioChannelsStreamRule(),
                    new AudioChannelLayoutStreamRule(),
                    new AudioLoudnessTruePeakStreamRule(),
                    new AudioLoudnessIntegratedTargetStreamRule(),
                ])
                ->help(__('nova.upload_audio_help')),

            Text::make(__('nova.path'), 'path')
                ->required()
                ->rules(['required', 'string', 'doesnt_start_with:/', 'doesnt_end_with:/', new StorageDirectoryExistsRule($fs)])
                ->help(__('nova.upload_audio_path_help')),
        ];
    }
}
