<?php

declare(strict_types=1);

use App\Actions\Storage\Wiki\UploadedFileAction;
use App\Constants\FeatureConstants;
use App\Rules\Wiki\Submission\Format\EncoderVersionFormatRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;
use Laravel\Pennant\Feature;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('fails when encoder version is older than required', function () {
    Feature::activate(FeatureConstants::REQUIRED_ENCODER_VERSION, 'Lavf59.27.100');

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
            'format' => [
                'tags' => [
                    'ENCODER' => 'Lavf58.76.100',
                ],
            ],
        ])),
    ]);

    $validator = Validator::make(
        ['file' => $file],
        ['file' => new EncoderVersionFormatRule()],
    );

    static::assertFalse($validator->passes());

    Process::assertRan(UploadedFileAction::formatFfprobeCommand($file));
});

test('fails when encoder version is up to date', function () {
    Feature::activate(FeatureConstants::REQUIRED_ENCODER_VERSION, 'Lavf59.27.100');

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
            'format' => [
                'tags' => [
                    'ENCODER' => 'Lavf59.27.100',
                ],
            ],
        ])),
    ]);

    $validator = Validator::make(
        ['file' => $file],
        ['file' => new EncoderVersionFormatRule()],
    );

    static::assertTrue($validator->passes());

    Process::assertRan(UploadedFileAction::formatFfprobeCommand($file));
});
