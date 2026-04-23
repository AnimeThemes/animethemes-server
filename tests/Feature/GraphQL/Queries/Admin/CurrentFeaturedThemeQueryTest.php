<?php

declare(strict_types=1);

use App\Models\Admin\FeaturedTheme;

test('current featured theme', function (): void {
    FeaturedTheme::factory()
        ->sequence(fn (): array => [
            FeaturedTheme::ATTRIBUTE_START_AT => now()->addDays(fake()->numberBetween(1, 10)),
            FeaturedTheme::ATTRIBUTE_END_AT => now()->addDays(fake()->numberBetween(11, 20)),
        ])
        ->count(fake()->randomDigitNotNull())
        ->create();

    $featuredTheme = FeaturedTheme::factory()->create();

    $response = $this->graphQL('
        query {
            currentfeaturedtheme {
                id
            }
        }
    ');

    $response->assertOk();
    $response->assertJson([
        'data' => [
            'currentfeaturedtheme' => [
                'id' => $featuredTheme->getKey(),
            ],
        ],
    ]);
});
