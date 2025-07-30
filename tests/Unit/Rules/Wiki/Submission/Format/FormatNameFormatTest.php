<?php

declare(strict_types=1);

use App\Actions\Storage\Wiki\UploadedFileAction;
use App\Rules\Wiki\Submission\Format\FormatNameFormatRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('fails when format name is not expected', function () {
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
                'format_name' => fake()->unique()->word(),
            ],
        ])),
    ]);

    $validator = Validator::make(
        ['file' => $file],
        ['file' => new FormatNameFormatRule(fake()->unique()->word())],
    );

    static::assertFalse($validator->passes());

    Process::assertRan(UploadedFileAction::formatFfprobeCommand($file));
});

test('passes when format name is expected', function () {
    $formatName = fake()->word();

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
                'format_name' => $formatName,
            ],
        ])),
    ]);

    $validator = Validator::make(
        ['file' => $file],
        ['file' => new FormatNameFormatRule($formatName)],
    );

    static::assertTrue($validator->passes());

    Process::assertRan(UploadedFileAction::formatFfprobeCommand($file));
});
