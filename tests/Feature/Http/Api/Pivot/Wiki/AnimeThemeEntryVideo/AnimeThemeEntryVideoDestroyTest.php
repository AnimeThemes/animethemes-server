<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\EntryVideo;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function (): void {
    $entryVideo = EntryVideo::factory()
        ->for(Entry::factory()->for(Theme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $response = delete(route('api.animethemeentryvideo.destroy', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video]));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $entryVideo = EntryVideo::factory()
        ->for(Entry::factory()->for(Theme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animethemeentryvideo.destroy', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video]));

    $response->assertForbidden();
});

test('not found', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->create();

    $video = Video::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Entry::class),
            CrudPermission::DELETE->format(Video::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animethemeentryvideo.destroy', ['animethemeentry' => $entry, 'video' => $video]));

    $response->assertNotFound();
});

test('deleted', function (): void {
    $entryVideo = EntryVideo::factory()
        ->for(Entry::factory()->for(Theme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Entry::class),
            CrudPermission::DELETE->format(Video::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animethemeentryvideo.destroy', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video]));

    $response->assertOk();
    $this->assertModelMissing($entryVideo);
});
