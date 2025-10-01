<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\UploadAudioAction as UploadAudio;
use App\Constants\Config\AudioConstants;
use App\Filament\Actions\Storage\Base\UploadAction;
use App\Models\Wiki\Audio;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\File as FileRule;

class UploadAudioAction extends UploadAction
{
    public static function getDefaultName(): ?string
    {
        return 'upload-audio';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.audio.upload.name'));

        $this->visible(Gate::allows('create', Audio::class));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  array<string, mixed>  $data
     */
    protected function storageAction(?Model $record, array $data): UploadAudio
    {
        /** @var UploadedFile $file */
        $file = Arr::get($data, 'file');

        /** @var string $path */
        $path = Arr::get($data, 'path');

        return new UploadAudio($file, $path);
    }

    public function disk(): string
    {
        return Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED);
    }

    /**
     * Get the file validation rules.
     */
    protected function fileRules(): array
    {
        return [
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
        ];
    }
}
