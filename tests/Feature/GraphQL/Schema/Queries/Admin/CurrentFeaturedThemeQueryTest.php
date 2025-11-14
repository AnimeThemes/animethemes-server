<?php

declare(strict_types=1);

use App\Models\Admin\FeaturedTheme;

use function Pest\Laravel\post;

test('current featured theme', function () {
    FeaturedTheme::factory()
        ->sequence(fn () => [
            FeaturedTheme::ATTRIBUTE_START_AT => now()->addDays(fake()->numberBetween(1, 10)),
            FeaturedTheme::ATTRIBUTE_END_AT => now()->addDays(fake()->numberBetween(11, 20)),
        ])
        ->count(fake()->randomDigitNotNull())
        ->create();

    $featuredTheme = FeaturedTheme::factory()->create();

    $response = post(route('graphql'), [
        'query' => '
            query {
                currentfeaturedtheme {
                    id
                }
            }
        ',
    ]);

    $response->assertOk();
    $response->assertJson([
        'data' => [
            'currentfeaturedtheme' => [
                'id' => $featuredTheme->getKey(),
            ],
        ],
    ]);
});
