<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $anime = Anime::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = $this->get(route('api.animeyear.index'));

    $response->assertJson(
        $anime->unique(Anime::ATTRIBUTE_YEAR)->sortBy(Anime::ATTRIBUTE_YEAR)->pluck(Anime::ATTRIBUTE_YEAR)->all(),
    );
});
