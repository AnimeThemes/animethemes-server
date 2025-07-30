<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->create();

    $video = Video::factory()->createOne();

    $response = $this->post(route('api.animethemeentryvideo.store', ['animethemeentry' => $entry, 'video' => $video]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->create();

    $video = Video::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.animethemeentryvideo.store', ['animethemeentry' => $entry, 'video' => $video]));

    $response->assertForbidden();
});

test('create', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->create();

    $video = Video::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::CREATE->format(AnimeThemeEntry::class),
            CrudPermission::CREATE->format(Video::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.animethemeentryvideo.store', ['animethemeentry' => $entry, 'video' => $video]));

    $response->assertCreated();
    static::assertDatabaseCount(AnimeThemeEntryVideo::class, 1);
});
