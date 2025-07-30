<?php

declare(strict_types=1);

use App\Actions\Models\Wiki\Video\Audio\BackfillAudioAction;
use App\Constants\Config\AudioConstants;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('skipped', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    $video = Video::factory()
        ->for(Audio::factory())
        ->createOne();

    $action = new BackfillAudioAction($video);

    $result = $action->handle();

    $this->assertTrue($result->getStatus() === ActionStatus::SKIPPED);
    $this->assertDatabaseCount(Audio::class, 1);
    $this->assertEmpty(Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles());
});

test('failed when no entries', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    $video = Video::factory()->createOne();

    $action = new BackfillAudioAction($video);

    $result = $action->handle();

    $this->assertTrue($result->hasFailed());
    $this->assertDatabaseCount(Audio::class, 0);
    $this->assertEmpty(Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles());
});

test('passes source video', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    $video = Video::factory()
        ->has(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->createOne([
            Video::ATTRIBUTE_PATH => fake()->word().'.webm',
        ]);

    Audio::factory()->createOne([
        Audio::ATTRIBUTE_PATH => Str::replace('webm', 'ogg', $video->path),
    ]);

    $action = new BackfillAudioAction($video);

    $result = $action->handle();

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertDatabaseCount(Audio::class, 1);
    $this->assertTrue($video->audio()->exists());
    $this->assertEmpty(Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles());
});

test('passes with higher priority source', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    $entry = AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory()));

    Video::factory()
        ->hasAttached($entry, [], Video::RELATION_ANIMETHEMEENTRIES)
        ->for(Audio::factory())
        ->createOne([
            Video::ATTRIBUTE_SOURCE => VideoSource::BD->value,
        ]);

    $video = Video::factory()
        ->hasAttached($entry, [], Video::RELATION_ANIMETHEMEENTRIES)
        ->createOne([
            Video::ATTRIBUTE_SOURCE => VideoSource::WEB->value,
        ]);

    $action = new BackfillAudioAction($video);

    $result = $action->handle();

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertDatabaseCount(Audio::class, 1);
    $this->assertTrue($video->audio()->exists());
    $this->assertEmpty(Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles());
});

test('passes with primary version source', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->createOne();

    $videoAttributes = Video::factory()->raw();

    $sourceAudio = Audio::factory()->createOne();

    Video::factory()
        ->has(AnimeThemeEntry::factory()->set(AnimeThemeEntry::ATTRIBUTE_VERSION, 1)->for($theme))
        ->for($sourceAudio)
        ->createOne($videoAttributes);

    Video::factory()
        ->has(AnimeThemeEntry::factory()->set(AnimeThemeEntry::ATTRIBUTE_VERSION, 2)->for($theme))
        ->for(Audio::factory())
        ->createOne($videoAttributes);

    $video = Video::factory()
        ->has(AnimeThemeEntry::factory()->set(AnimeThemeEntry::ATTRIBUTE_VERSION, 3)->for($theme))
        ->createOne($videoAttributes);

    $action = new BackfillAudioAction($video);

    $result = $action->handle();

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertDatabaseCount(Audio::class, 2);
    $this->assertTrue($video->audio()->is($sourceAudio));
    $this->assertEmpty(Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles());
});
