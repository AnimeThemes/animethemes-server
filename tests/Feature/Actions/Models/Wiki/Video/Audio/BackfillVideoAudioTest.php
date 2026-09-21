<?php

declare(strict_types=1);

use App\Actions\Models\Wiki\Video\Audio\BackfillAudioAction;
use App\Constants\Config\AudioConstants;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

pest()->use(WithFaker::class);

test('skipped', function (): void {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    $video = Video::factory()
        ->for(Audio::factory())
        ->createOne();

    $action = new BackfillAudioAction($video);

    $result = $action->handle();

    expect($result->getStatus())->toBe(ActionStatus::SKIPPED);
    $this->assertDatabaseCount(Audio::class, 1);
    expect(Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles())->toBeEmpty();
});

test('failed when no entries', function (): void {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    $video = Video::factory()->createOne();

    $action = new BackfillAudioAction($video);

    $result = $action->handle();

    expect($result->hasFailed())->toBeTrue();
    $this->assertDatabaseCount(Audio::class, 0);
    expect(Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles())->toBeEmpty();
});

test('passes source video', function (): void {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    $video = Video::factory()
        ->has(Entry::factory()->for(Theme::factory()->for(Anime::factory())))
        ->createOne([
            Video::ATTRIBUTE_PATH => fake()->word().'.webm',
        ]);

    Audio::factory()->createOne([
        Audio::ATTRIBUTE_PATH => Str::replace('webm', 'ogg', $video->path),
    ]);

    $action = new BackfillAudioAction($video);

    $result = $action->handle();

    expect($result->getStatus())->toBe(ActionStatus::PASSED);
    $this->assertDatabaseCount(Audio::class, 1);
    expect($video->audio()->exists())->toBeTrue();
    expect(Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles())->toBeEmpty();
});

test('passes with higher priority source', function (): void {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    $entry = Entry::factory()->for(Theme::factory()->for(Anime::factory()));

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

    expect($result->getStatus())->toBe(ActionStatus::PASSED);
    $this->assertDatabaseCount(Audio::class, 1);
    expect($video->audio()->exists())->toBeTrue();
    expect(Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles())->toBeEmpty();
});

test('passes with primary version source', function (): void {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    $theme = Theme::factory()
        ->for(Anime::factory())
        ->createOne();

    $videoAttributes = Video::factory()->raw();

    $sourceAudio = Audio::factory()->createOne();

    Video::factory()
        ->has(Entry::factory()->set(Entry::ATTRIBUTE_VERSION, 1)->for($theme))
        ->for($sourceAudio)
        ->createOne($videoAttributes);

    Video::factory()
        ->has(Entry::factory()->set(Entry::ATTRIBUTE_VERSION, 2)->for($theme))
        ->for(Audio::factory())
        ->createOne($videoAttributes);

    $video = Video::factory()
        ->has(Entry::factory()->set(Entry::ATTRIBUTE_VERSION, 3)->for($theme))
        ->createOne($videoAttributes);

    $action = new BackfillAudioAction($video);

    $result = $action->handle();

    expect($result->getStatus())->toBe(ActionStatus::PASSED);
    $this->assertDatabaseCount(Audio::class, 2);
    expect($video->audio()->is($sourceAudio))->toBeTrue();
    expect(Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles())->toBeEmpty();
});
