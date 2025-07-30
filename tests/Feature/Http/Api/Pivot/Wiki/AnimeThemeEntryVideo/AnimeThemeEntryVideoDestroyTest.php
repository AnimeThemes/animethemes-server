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

use function Pest\Laravel\delete;

test('protected', function () {
    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $response = delete(route('api.animethemeentryvideo.destroy', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animethemeentryvideo.destroy', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video]));

    $response->assertForbidden();
});

test('not found', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->create();

    $video = Video::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(AnimeThemeEntry::class),
            CrudPermission::DELETE->format(Video::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animethemeentryvideo.destroy', ['animethemeentry' => $entry, 'video' => $video]));

    $response->assertNotFound();
});

test('deleted', function () {
    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(AnimeThemeEntry::class),
            CrudPermission::DELETE->format(Video::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animethemeentryvideo.destroy', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video]));

    $response->assertOk();
    $this->assertModelMissing($entryVideo);
});
