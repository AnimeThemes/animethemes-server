<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Group;
use App\Models\Wiki\Theme;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('nameable', function (): void {
    $group = Group::factory()->createOne();

    expect($group->getName())->toBeString();
});

test('has subtitle', function (): void {
    $group = Group::factory()->createOne();

    expect($group->getSubtitle())->toBeString();
});

test('themes', function (): void {
    $themeCount = fake()->randomDigitNotNull();

    $group = Group::factory()
        ->has(Theme::factory()->for(Anime::factory())->count($themeCount))
        ->createOne();

    expect($group->themes())->toBeInstanceOf(HasMany::class);
    expect($group->themes()->count())->toEqual($themeCount);
    expect($group->themes()->first())->toBeInstanceOf(Theme::class);
});
