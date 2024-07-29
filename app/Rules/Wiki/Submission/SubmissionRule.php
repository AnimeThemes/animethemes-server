<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

/**
 * Class SubmissionRule.
 */
abstract class SubmissionRule implements ValidationRule, ValidatorAwareRule
{
    /**
     * The stream, chapter and format data as inspected by ffprobe.
     *
     * @var array
     */
    public static array $ffprobeData = [];

    /**
     * The loudness stats of the input file as parsed by the ffmpeg audio filter.
     *
     * @var array
     */
    protected array $loudnessStats;

    /**
     * Set the current validator.
     *
     * @param  Validator  $validator
     * @return $this
     */
    public function setValidator(Validator $validator): self
    {
        /** @var UploadedFile|array $files */
        $files = Arr::get($validator->getData(), 'file');

        foreach ($files as $file) {
            /** @var UploadedFile $file*/

            $ffprobeData = Arr::get($validator->getData(), 'ffprobeData');
            if ($ffprobeData === null && $file !== null) {
                $ffprobeData = $this->getFFprobeData($file);
                $validator->setValue('ffprobeData', $ffprobeData);
            }
            static::$ffprobeData = $ffprobeData;

            $loudnessStats = Arr::get($validator->getData(), 'loudnessStats');
            if ($loudnessStats === null && $file !== null) {
                $loudnessStats = $this->getLoudnessStats($file);
                $validator->setValue('loudnessStats', $loudnessStats);
            }
            $this->loudnessStats = $loudnessStats;
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
        return Arr::get(static::$ffprobeData, 'streams', []);
    }

    /**
     * Get submission format.
     *
     * @return array
     */
    protected function format(): array
    {
        return Arr::get(static::$ffprobeData, 'format', []);
    }

    /**
     * Get submission chapters.
     *
     * @return array
     */
    protected function chapters(): array
    {
        return Arr::get(static::$ffprobeData, 'chapters', []);
    }

    /**
     * Get the submission loudness stats.
     *
     * @return array
     */
    protected function loudness(): array
    {
        return $this->loudnessStats;
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
            '/dev/null',
        ];

        return Arr::join($arguments, ' ');
    }
}
