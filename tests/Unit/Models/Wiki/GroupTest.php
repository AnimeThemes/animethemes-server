<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Group;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('nameable', function () {
    $group = Group::factory()->createOne();

    static::assertIsString($group->getName());
});

test('has subtitle', function () {
    $group = Group::factory()->createOne();

    static::assertIsString($group->getSubtitle());
});

test('themes', function () {
    $themeCount = fake()->randomDigitNotNull();

    $group = Group::factory()
        ->has(AnimeTheme::factory()->for(Anime::factory())->count($themeCount))
        ->createOne();

    static::assertInstanceOf(HasMany::class, $group->animethemes());
    static::assertEquals($themeCount, $group->animethemes()->count());
    static::assertInstanceOf(AnimeTheme::class, $group->animethemes()->first());
});
