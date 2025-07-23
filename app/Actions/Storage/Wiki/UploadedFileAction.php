<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class UploadedFileAction
{
    /**
     * The stream, chapter and format data as inspected by ffprobe.
     *
     * @var array
     */
    public array $ffprobeData = [];

    /**
     * The loudness stats of the input file as parsed by the ffmpeg audio filter.
     *
     * @var array
     */
    public array $loudnessStats = [];

    public function __construct(protected UploadedFile $file)
    {
        $this->setFFprobeData();
    }

    /**
     * Set the FFprobe data for the uploaded file.
     */
    protected function setFFprobeData(): void
    {
        $command = static::formatFfprobeCommand($this->file);

        $result = Process::run($command)->throw();

        $this->ffprobeData = json_decode($result->output(), true);
    }

    /**
     * Set the loudness stats for the uploaded file.
     *
     * @return array
     */
    public function setLoudnessStats(): array
    {
        $command = static::formatLoudnessCommand($this->file);

        $result = Process::run($command)->throw();

        $loudness = Str::match('/{[^}]*}/m', $result->errorOutput());

        $this->loudnessStats = json_decode($loudness, true);

        return $this->loudnessStats;
    }

    /**
     * Get submission streams.
     *
     * @return array
     */
    public function streams(): array
    {
        return Arr::get($this->ffprobeData, 'streams', []);
    }

    /**
     * Get submission format.
     *
     * @return array
     */
    public function format(): array
    {
        return Arr::get($this->ffprobeData, 'format', []);
    }

    /**
     * Get submission chapters.
     *
     * @return array
     */
    public function chapters(): array
    {
        return Arr::get($this->ffprobeData, 'chapters', []);
    }

    /**
     * Get the resolution.
     */
    public function resolution(): int
    {
        return intval(Arr::get($this->ffprobeData, 'streams.0.height', 0));
    }

    /**
     * For WebMs, tags will be contained in the format object.
     * For Audios, tags will be contained in the stream object.
     *
     * @return array
     */
    public function tags(): array
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
     * Get the submission loudness stats.
     *
     * @return array
     */
    public function loudness(): array
    {
        return $this->loudnessStats;
    }

    /**
     * Format FFprobe command.
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
