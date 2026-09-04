<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\EntryVideo;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

uses(WithFaker::class);

test('protected', function (): void {
    $featuredTheme = FeaturedTheme::factory()->createOne();

    $parameters = FeaturedTheme::factory()->raw();

    $response = put(route('api.featuredtheme.update', ['featuredtheme' => $featuredTheme] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $featuredTheme = FeaturedTheme::factory()->createOne();

    $parameters = FeaturedTheme::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.featuredtheme.update', ['featuredtheme' => $featuredTheme] + $parameters));

    $response->assertForbidden();
});

test('start at before end date', function (): void {
    $featuredTheme = FeaturedTheme::factory()->createOne();

    $parameters = FeaturedTheme::factory()->raw([
        FeaturedTheme::ATTRIBUTE_START_AT => fake()->dateTimeBetween('+1 day', '+1 year')->format(AllowedDateFormat::YMDHISU->value),
        FeaturedTheme::ATTRIBUTE_END_AT => fake()->dateTimeBetween('-1 year', '-1 day')->format(AllowedDateFormat::YMDHISU->value),
    ]);

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(FeaturedTheme::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.featuredtheme.update', ['featuredtheme' => $featuredTheme] + $parameters));

    $response->assertJsonValidationErrors([
        FeaturedTheme::ATTRIBUTE_START_AT,
        FeaturedTheme::ATTRIBUTE_END_AT,
    ]);
});

test('anime theme entry video exists', function (): void {
    $featuredTheme = FeaturedTheme::factory()->createOne();

    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->create();

    $video = Video::factory()->create();

    $parameters = FeaturedTheme::factory()->raw([
        FeaturedTheme::ATTRIBUTE_ENTRY => $entry->getKey(),
        FeaturedTheme::ATTRIBUTE_VIDEO => $video->getKey(),
    ]);

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(FeaturedTheme::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.featuredtheme.update', ['featuredtheme' => $featuredTheme] + $parameters));

    $response->assertJsonValidationErrors([
        FeaturedTheme::ATTRIBUTE_ENTRY,
        FeaturedTheme::ATTRIBUTE_VIDEO,
    ]);
});

test('update', function (): void {
    $featuredTheme = FeaturedTheme::factory()->createOne();

    $entryVideo = EntryVideo::factory()
        ->for(Entry::factory()->for(Theme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $parameters = FeaturedTheme::factory()->raw([
        FeaturedTheme::ATTRIBUTE_ENTRY => $entryVideo->entry_id,
        FeaturedTheme::ATTRIBUTE_VIDEO => $entryVideo->video_id,
    ]);

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(FeaturedTheme::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.featuredtheme.update', ['featuredtheme' => $featuredTheme] + $parameters));

    $response->assertOk();
});
