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

    $this->assertIsString($group->getName());
});

test('has subtitle', function () {
    $group = Group::factory()->createOne();

    $this->assertIsString($group->getSubtitle());
});

test('themes', function () {
    $themeCount = fake()->randomDigitNotNull();

    $group = Group::factory()
        ->has(AnimeTheme::factory()->for(Anime::factory())->count($themeCount))
        ->createOne();

    $this->assertInstanceOf(HasMany::class, $group->animethemes());
    $this->assertEquals($themeCount, $group->animethemes()->count());
    $this->assertInstanceOf(AnimeTheme::class, $group->animethemes()->first());
});
