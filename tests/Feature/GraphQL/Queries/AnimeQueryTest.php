<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('anime years query', function () {
    static::markTestSkipped('TODO');

    /** @phpstan-ignore-next-line */
    $years = Anime::factory()
        ->count(fake()->randomDigitNotNull())
        ->create()
        ->map(fn (Anime $anime) => $anime->getAttribute(Anime::ATTRIBUTE_YEAR))
        ->sortBy(fn (int $year) => $year)
        ->unique()
        ->values()
        ->toArray();

    $response = $this->graphQL('
        {
            animeyears
        }
        ');

    $response->assertJson([
        'data' => [
            'animeyears' => $years,
        ],
    ]);
});
