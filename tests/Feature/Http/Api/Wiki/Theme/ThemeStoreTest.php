<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\ThemeType;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Theme;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function (): void {
    $theme = Theme::factory()->for(Anime::factory())->makeOne();

    $response = post(route('api.animetheme.store', $theme->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $theme = Theme::factory()->for(Anime::factory())->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.animetheme.store', $theme->toArray()));

    $response->assertForbidden();
});

test('required fields', function (): void {
    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Theme::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.animetheme.store'));

    $response->assertJsonValidationErrors([
        Theme::ATTRIBUTE_ANIME,
        Theme::ATTRIBUTE_SLUG,
        Theme::ATTRIBUTE_TYPE,
    ]);
});

test('create', function (): void {
    $anime = Anime::factory()->createOne();

    $type = Arr::random(ThemeType::cases());

    $parameters = array_merge(
        Theme::factory()->raw(),
        [Theme::ATTRIBUTE_TYPE => $type->localize()],
        [Theme::ATTRIBUTE_ANIME => $anime->getKey()],
    );

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Theme::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.animetheme.store', $parameters));

    $response->assertCreated();
    $this->assertDatabaseCount(Theme::class, 1);
});
