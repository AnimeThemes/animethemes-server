<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission;

use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

/**
 * Class SubmissionRule.
 */
abstract class SubmissionRule implements DataAwareRule, Rule, ValidatorAwareRule
{
    /**
     * The data under validation.
     *
     * @var array
     */
    protected array $data = [];

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set the current validator.
     *
     * @param  Validator  $validator
     * @return $this
     */
    public function setValidator(Validator $validator): self
    {
        /** @var UploadedFile|null $file */
        $file = Arr::get($validator->getData(), 'file');

        $ffprobeData = Arr::get($validator->getData(), 'ffprobeData');
        if ($ffprobeData === null && $file !== null) {
            $validator->setValue('ffprobeData', $this->getFFprobeData($file));
        }

        $loudnessStats = Arr::get($validator->getData(), 'loudnessStats');
        if ($loudnessStats === null && $file !== null) {
            $validator->setValue('loudnessStats', $this->getLoudnessStats($file));
        }

        return $this;
    }

    /**
     * Get the FFprobe data for the uploaded file.
     *
     * @param  UploadedFile  $file
     * @return array
     */
    private function getFFprobeData(UploadedFile $file): array
    {
        $command = static::formatFfprobeCommand($file);

        $result = Process::run($command)->throw();

        return json_decode($result->output(), true);
    }

    /**
     * Get the loudness stats for the uploaded file.
     *
     * @param  UploadedFile  $file
     * @return array
     */
    private function getLoudnessStats(UploadedFile $file): array
    {
        $command = static::formatLoudnessCommand($file);

        $result = Process::run($command)->throw();

        $loudness = Str::match('/{[^}]*}/m', $result->errorOutput());

        return json_decode($loudness, true);
    }

    /**
     * Get submission streams.
     *
     * @return array
     */
    protected function streams(): array
    {
        return Arr::get($this->data, 'ffprobeData.streams', []);
    }

    /**
     * Get submission format.
     *
     * @return array
     */
    protected function format(): array
    {
        return Arr::get($this->data, 'ffprobeData.format', []);
    }

    /**
     * Get submission chapters.
     *
     * @return array
     */
    protected function chapters(): array
    {
        return Arr::get($this->data, 'ffprobeData.chapters', []);
    }

    /**
     * Get the submission loudness stats.
     *
     * @return array
     */
    protected function loudness(): array
    {
        return Arr::get($this->data, 'loudnessStats', []);
    }

    /**
     * For WebMs, tags will be contained in the format object.
     * For Audios, tags will be contained in the stream object.
     *
     * @return array
     */
    protected function tags(): array
    {
        $format = $this->format();
        if (Arr::has($format, 'tags')) {
            $tags = Arr::get($format, 'tags');

            return array_change_key_case($tags);
        }

        $audio = Arr::first(
            $this->streams(),
            fn (array $stream) => Arr::get($stream, 'codec_type') === 'audio'
        );

        $tags = Arr::get($audio, 'tags', []);

        return array_change_key_case($tags);
    }

    /**
     * Format FFprobe command.
     *
     * @param  UploadedFile  $file
     * @return string
     */
    public static function formatFfprobeCommand(UploadedFile $file): string
    {
        $arguments = [
            'ffprobe',
            '-v',
            'quiet',
            '-print_format',
            'json',
            '-show_streams',
            '-show_format',
            '-show_chapters',
            $file->path(),
        ];

        return Arr::join($arguments, ' ');
    }

    /**
     * Format loudness command.
     *
     * @param  UploadedFile  $file
     * @return string
     */
    public static function formatLoudnessCommand(UploadedFile $file): string
    {
        $arguments = [
            'ffmpeg',
            '-i',
            $file->path(),
            '-hide_banner',
            '-nostats',
            '-vn',
            '-sn',
            '-dn',
            '-filter:a',
            'loudnorm=I=-16:LRA=20:TP=-1:dual_mono=true:linear=true:print_format=json',
            '-f',
            'null',
            '/dev/null'
        ];

        return Arr::join($arguments, ' ');
    }
}
