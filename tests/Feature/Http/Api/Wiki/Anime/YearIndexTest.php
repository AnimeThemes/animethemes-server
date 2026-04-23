<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use Illuminate\Foundation\Testing\WithFaker;

use function Pest\Laravel\get;

uses(WithFaker::class);

test('default', function (): void {
    $anime = Anime::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.animeyear.index'));

    $response->assertJson(
        $anime->unique(Anime::ATTRIBUTE_YEAR)->sortBy(Anime::ATTRIBUTE_YEAR)->pluck(Anime::ATTRIBUTE_YEAR)->all(),
    );
});
