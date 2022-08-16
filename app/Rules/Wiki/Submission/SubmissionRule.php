<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission;

use FFMpeg\FFProbe\DataMapping\Format;
use FFMpeg\FFProbe\DataMapping\StreamCollection;
use FFMpeg\FFProbe\Mapper;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use ProtoneMedia\LaravelFFMpeg\FFMpeg\FFProbe;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

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
    public function setData($data): self
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
    public function setValidator($validator): self
    {
        /** @var UploadedFile|null $file */
        $file = Arr::get($validator->getData(), 'video');

        $ffprobeData = Arr::get($validator->getData(), 'ffprobeData');
        if ($ffprobeData === null && $file !== null) {
            $data = array_merge($validator->getData(), ['ffprobeData' => $this->getFFprobeData($file)]);

            $validator->setData($data);
        }

        $loudnessStats = Arr::get($validator->getData(), 'loudnessStats');
        if ($loudnessStats === null && $file !== null) {
            $data = array_merge($validator->getData(), ['loudnessStats' => $this->getLoudnessStats($file)]);

            $validator->setData($data);
        }

        return $this;
    }

    /**
     * Get the FFprobe data for the uploaded file.
     *
     * @param  UploadedFile  $file
     * @return array
     */
    protected function getFFprobeData(UploadedFile $file): array
    {
        $commands = [
            $file->path(),
            '-v',
            'quiet',
            '-print_format',
            'json',
            '-show_streams',
            '-show_format',
            '-show_chapters'
        ];

        $output = FFProbe::create()
            ->getFFProbeDriver()
            ->command($commands);

        return json_decode($output, true);
    }

    /**
     * Get the loudness stats for the uploaded file.
     *
     * @param  UploadedFile  $file
     * @return array
     */
    protected function getLoudnessStats(UploadedFile $file): array
    {
        $filter = [
            '-hide_banner',
            '-nostats',
            '-vn',
            '-sn',
            '-dn',
            '-filter:a',
            'loudnorm=I=-16:LRA=20:TP=-1:dual_mono=true:linear=true:print_format=json',
            '-f',
            'null',
        ];

        $output = FFMpeg::open($file)
            ->export()
            ->addFilter($filter)
            ->getProcessOutput();

        $output = Arr::join($output->all(), '');
        $loudness = Str::match('/{[^}]*}/m', $output);

        return json_decode($loudness, true);
    }

    /**
     * Get submission streams.
     *
     * @return StreamCollection
     */
    protected function streams(): StreamCollection
    {
        $ffprobeData = Arr::get($this->data, 'ffprobeData');

        $mapper = new Mapper();

        return $mapper->map('streams', $ffprobeData);
    }

    /**
     * Get submission format.
     *
     * @return Format
     */
    protected function format(): Format
    {
        $ffprobeData = Arr::get($this->data, 'ffprobeData');

        $mapper = new Mapper();

        return $mapper->map('format', $ffprobeData);
    }

    /**
     * Get submission chapters.
     *
     * @return array
     */
    protected function chapters(): array
    {
        return Arr::get($this->data, 'ffprobeData.chapters');
    }

    /**
     * Get the submission loudness stats.
     *
     * @return array
     */
    protected function loudness(): array
    {
        return Arr::get($this->data, 'loudnessStats');
    }
}
