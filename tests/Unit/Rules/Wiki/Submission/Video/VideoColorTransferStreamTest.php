<?php

declare(strict_types=1);

use App\Actions\Storage\Wiki\UploadedFileAction;
use App\Constants\FeatureConstants;
use App\Rules\Wiki\Submission\Video\VideoColorTransferStreamRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;
use Laravel\Pennant\Feature;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('fails when color transfer is not accepted', function () {
    Feature::activate(FeatureConstants::VIDEO_COLOR_TRANSFER_STREAM, 'bt709,smpte170m,bt470bg');

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
                    'codec_type' => 'video',
                    'color_transfer' => fake()->word(),
                ],
            ],
        ])),
    ]);

    $validator = Validator::make(
        ['file' => $file],
        ['file' => new VideoColorTransferStreamRule()],
    );

    static::assertFalse($validator->passes());

    Process::assertRan(UploadedFileAction::formatFfprobeCommand($file));
});

test('passes when color transfer is accepted', function () {
    Feature::activate(FeatureConstants::VIDEO_COLOR_TRANSFER_STREAM, 'bt709,smpte170m,bt470bg');

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
                    'codec_type' => 'video',
                    'color_transfer' => Arr::random(['bt709', 'smpte170m', 'bt470bg']),
                ],
            ],
        ])),
    ]);

    $validator = Validator::make(
        ['file' => $file],
        ['file' => new VideoColorTransferStreamRule()],
    );

    static::assertTrue($validator->passes());

    Process::assertRan(UploadedFileAction::formatFfprobeCommand($file));
});
