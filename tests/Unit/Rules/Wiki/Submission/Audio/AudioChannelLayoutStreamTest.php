<?php

declare(strict_types=1);

use App\Actions\Storage\Wiki\UploadedFileAction;
use App\Rules\Wiki\Submission\Audio\AudioChannelLayoutStreamRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('fails when layout is not stereo', function () {
    $file = UploadedFile::fake()->create(fake()->word().'.webm', fake()->randomDigitNotNull());

    Process::fake([
        UploadedFileAction::formatLoudnessCommand($file) => Process::result(errorOutput: json_encode([
            'input_i' => fake()->randomFloat(),
            'input_tp' => fake()->randomFloat(),
            'input_lra' => fake()->randomFloat(),
            'input_thresh' => fake()->randomFloat(),
            'output_i' => fake()->randomFloat(),
            'output_tp' => fake()->randomFloat(),
            'output_lra' => fake()->randomFloat(),
            'output_thresh' => fake()->randomFloat(),
            'normalization_type' => 'dynamic',
            'target_offset' => fake()->randomFloat(),
        ])),
        UploadedFileAction::formatFfprobeCommand($file) => Process::result(json_encode([
            'streams' => [
                0 => [
                    'codec_type' => 'audio',
                    'channel_layout' => fake()->word(),
                ],
            ],
        ])),
    ]);

    $validator = Validator::make(
        ['file' => $file],
        ['file' => new AudioChannelLayoutStreamRule()],
    );

    static::assertFalse($validator->passes());

    Process::assertRan(UploadedFileAction::formatFfprobeCommand($file));
});

test('passes when layout is stereo', function () {
    $file = UploadedFile::fake()->create(fake()->word().'.webm', fake()->randomDigitNotNull());

    Process::fake([
        UploadedFileAction::formatLoudnessCommand($file) => Process::result(errorOutput: json_encode([
            'input_i' => fake()->randomFloat(),
            'input_tp' => fake()->randomFloat(),
            'input_lra' => fake()->randomFloat(),
            'input_thresh' => fake()->randomFloat(),
            'output_i' => fake()->randomFloat(),
            'output_tp' => fake()->randomFloat(),
            'output_lra' => fake()->randomFloat(),
            'output_thresh' => fake()->randomFloat(),
            'normalization_type' => 'dynamic',
            'target_offset' => fake()->randomFloat(),
        ])),
        UploadedFileAction::formatFfprobeCommand($file) => Process::result(json_encode([
            'streams' => [
                0 => [
                    'codec_type' => 'audio',
                    'channel_layout' => 'stereo',
                ],
            ],
        ])),
    ]);

    $validator = Validator::make(
        ['file' => $file],
        ['file' => new AudioChannelLayoutStreamRule()],
    );

    static::assertTrue($validator->passes());

    Process::assertRan(UploadedFileAction::formatFfprobeCommand($file));
});
