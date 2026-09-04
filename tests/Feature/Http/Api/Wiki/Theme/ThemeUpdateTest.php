<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\ThemeType;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Theme;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

test('protected', function (): void {
    $theme = Theme::factory()->for(Anime::factory())->createOne();

    $type = Arr::random(ThemeType::cases());

    $parameters = array_merge(
        Theme::factory()->raw(),
        [Theme::ATTRIBUTE_TYPE => $type->localize()],
    );

    $response = put(route('api.animetheme.update', ['animetheme' => $theme] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $theme = Theme::factory()->for(Anime::factory())->createOne();

    $type = Arr::random(ThemeType::cases());

    $parameters = array_merge(
        Theme::factory()->raw(),
        [Theme::ATTRIBUTE_TYPE => $type->localize()],
    );

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.animetheme.update', ['animetheme' => $theme] + $parameters));

    $response->assertForbidden();
});

test('trashed', function (): void {
    $theme = Theme::factory()
        ->trashed()
        ->for(Anime::factory())
        ->createOne();

    $type = Arr::random(ThemeType::cases());

    $parameters = array_merge(
        Theme::factory()->raw(),
        [Theme::ATTRIBUTE_TYPE => $type->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Theme::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.animetheme.update', ['animetheme' => $theme] + $parameters));

    $response->assertNotFound();
});

test('update', function (): void {
    $theme = Theme::factory()->for(Anime::factory())->createOne();

    $type = Arr::random(ThemeType::cases());

    $parameters = array_merge(
        Theme::factory()->raw(),
        [Theme::ATTRIBUTE_TYPE => $type->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Theme::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.animetheme.update', ['animetheme' => $theme] + $parameters));

    $response->assertOk();
});
