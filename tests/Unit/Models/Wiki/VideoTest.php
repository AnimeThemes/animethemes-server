<?php

declare(strict_types=1);

use App\Constants\Config\VideoConstants;
use App\Enums\Models\List\PlaylistVisibility;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Events\Wiki\Video\VideoForceDeleting;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use CyrildeWit\EloquentViewable\View;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

uses(WithFaker::class);

test('casts overlap to enum', function () {
    $video = Video::factory()->createOne();

    $overlap = $video->overlap;

    static::assertInstanceOf(VideoOverlap::class, $overlap);
});

test('casts source to enum', function () {
    $video = Video::factory()->createOne();

    $source = $video->source;

    static::assertInstanceOf(VideoSource::class, $source);
});

test('searchable as', function () {
    $video = Video::factory()->createOne();

    static::assertIsString($video->searchableAs());
});

test('to searchable array', function () {
    $video = Video::factory()->createOne();

    static::assertIsArray($video->toSearchableArray());
});

test('nameable', function () {
    $video = Video::factory()->createOne();

    static::assertIsString($video->getName());
});

test('has subtitle', function () {
    $video = Video::factory()->createOne();

    static::assertIsString($video->getSubtitle());
});

test('views', function () {
    $video = Video::factory()->createOne();

    views($video)->record();

    static::assertInstanceOf(MorphMany::class, $video->views());
    static::assertEquals(1, $video->views()->count());
    static::assertInstanceOf(View::class, $video->views()->first());
});

test('appends tags', function () {
    $video = Video::factory()->createOne();

    static::assertArrayHasKey(Video::ATTRIBUTE_TAGS, $video);
});

test('nc tag', function () {
    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_NC => true,
    ]);

    static::assertContains('NC', $video->tags);
});

test('no nc tag', function () {
    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_NC => false,
    ]);

    static::assertNotContains('NC', $video->tags);
});

test('dvd tag', function () {
    $source = VideoSource::DVD;

    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_SOURCE => $source->value,
    ]);

    static::assertContains($source->localize(), $video->tags);
});

test('bd tag', function () {
    $source = VideoSource::BD;

    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_SOURCE => $source->value,
    ]);

    static::assertContains($source->localize(), $video->tags);
});

test('other source tag', function () {
    $source = null;
    while ($source === null) {
        $sourceCandidate = Arr::random(VideoSource::cases());
        if ($sourceCandidate !== VideoSource::BD && $sourceCandidate !== VideoSource::DVD) {
            $source = $sourceCandidate;
        }
    }

    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_SOURCE => $source->value,
    ]);

    static::assertNotContains($source->localize(), $video->tags);
});

test('resolution tag', function () {
    $video = Video::factory()->createOne();

    static::assertContains(strval($video->resolution), $video->tags);
});

test('no720 resolution tag', function () {
    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_RESOLUTION => 720,
    ]);

    static::assertNotContains(strval($video->resolution), $video->tags);
});

test('subbed tag', function () {
    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_SUBBED => true,
    ]);

    static::assertContains('Subbed', $video->tags);
    static::assertNotContains('Lyrics', $video->tags);
});

test('lyrics tag', function () {
    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_SUBBED => false,
        Video::ATTRIBUTE_LYRICS => true,
    ]);

    static::assertNotContains('Subbed', $video->tags);
    static::assertContains('Lyrics', $video->tags);
});

test('source priority', function (array $a, array $b) {
    $first = Video::factory()->createOne($a);

    $second = Video::factory()->createOne($b);

    static::assertGreaterThan($first->getSourcePriority(), $second->getSourcePriority());
})->with('priorityProvider');
test('entries', function () {
    $entryCount = fake()->randomDigitNotNull();

    $video = Video::factory()
        ->has(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory()))->count($entryCount))
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $video->animethemeentries());
    static::assertEquals($entryCount, $video->animethemeentries()->count());
    static::assertInstanceOf(AnimeThemeEntry::class, $video->animethemeentries()->first());
    static::assertEquals(AnimeThemeEntryVideo::class, $video->animethemeentries()->getPivotClass());
});

test('audio', function () {
    $video = Video::factory()
        ->for(Audio::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $video->audio());
    static::assertInstanceOf(Audio::class, $video->audio()->first());
});

test('tracks public', function () {
    $trackCount = fake()->randomDigitNotNull();

    $playlist = Playlist::factory()->createOne([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC]);
    $video = Video::factory()
        ->has(PlaylistTrack::factory()->for($playlist)->count($trackCount), Video::RELATION_TRACKS)
        ->createOne();

    static::assertInstanceOf(HasMany::class, $video->tracks());
    static::assertEquals($trackCount, $video->tracks()->count());
    static::assertInstanceOf(PlaylistTrack::class, $video->tracks()->first());
});

test('tracks not public', function () {
    $trackCount = fake()->randomDigitNotNull();

    $visibility = Arr::random([PlaylistVisibility::PRIVATE, PlaylistVisibility::UNLISTED]);
    $playlist = Playlist::factory()->createOne([Playlist::ATTRIBUTE_VISIBILITY => $visibility]);
    $video = Video::factory()
        ->has(PlaylistTrack::factory()->for($playlist)->count($trackCount), Video::RELATION_TRACKS)
        ->createOne();

    static::assertInstanceOf(HasMany::class, $video->tracks());
    static::assertNotEquals($trackCount, $video->tracks()->count());
});

test('script', function () {
    $video = Video::factory()
        ->has(VideoScript::factory(), Video::RELATION_SCRIPT)
        ->createOne();

    static::assertInstanceOf(HasOne::class, $video->videoscript());
    static::assertInstanceOf(VideoScript::class, $video->videoscript()->first());
});
/**
 * Provider for source priority testing.
 *
 * @return array
 */
dataset('priorityProvider', function () {
    return [
        [
            [
                Video::ATTRIBUTE_SOURCE => VideoSource::WEB->value,
            ],
            [
                Video::ATTRIBUTE_SOURCE => VideoSource::BD->value,
            ],
        ],
        [
            [
                Video::ATTRIBUTE_SOURCE => VideoSource::BD->value,
                Video::ATTRIBUTE_OVERLAP => VideoOverlap::OVER->value,
                Video::ATTRIBUTE_LYRICS => false,
                Video::ATTRIBUTE_SUBBED => false,
            ],
            [
                Video::ATTRIBUTE_SOURCE => VideoSource::BD->value,
                Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE->value,
                Video::ATTRIBUTE_LYRICS => false,
                Video::ATTRIBUTE_SUBBED => false,
            ],
        ],
        [
            [
                Video::ATTRIBUTE_SOURCE => VideoSource::BD->value,
                Video::ATTRIBUTE_OVERLAP => VideoOverlap::TRANS->value,
                Video::ATTRIBUTE_LYRICS => false,
                Video::ATTRIBUTE_SUBBED => false,
            ],
            [
                Video::ATTRIBUTE_SOURCE => VideoSource::BD->value,
                Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE->value,
                Video::ATTRIBUTE_LYRICS => false,
                Video::ATTRIBUTE_SUBBED => false,
            ],
        ],
        [
            [
                Video::ATTRIBUTE_SOURCE => VideoSource::BD->value,
                Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE->value,
                Video::ATTRIBUTE_LYRICS => true,
                Video::ATTRIBUTE_SUBBED => false,
            ],
            [
                Video::ATTRIBUTE_SOURCE => VideoSource::BD->value,
                Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE->value,
                Video::ATTRIBUTE_LYRICS => false,
                Video::ATTRIBUTE_SUBBED => false,
            ],
        ],
        [
            [
                Video::ATTRIBUTE_SOURCE => VideoSource::BD->value,
                Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE->value,
                Video::ATTRIBUTE_LYRICS => false,
                Video::ATTRIBUTE_SUBBED => true,
            ],
            [
                Video::ATTRIBUTE_SOURCE => VideoSource::BD->value,
                Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE,
                Video::ATTRIBUTE_LYRICS => false,
                Video::ATTRIBUTE_SUBBED => false,
            ],
        ],
    ];
});

test('video storage deletion', function () {
    $fs = Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    $file = File::fake()->create(fake()->word().'.webm', fake()->randomDigitNotNull());
    $fsFile = $fs->putFile('', $file);

    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_PATH => $fsFile,
    ]);

    $video->delete();

    static::assertTrue($fs->exists($video->path));
});

test('video storage force deletion', function () {
    Event::fakeExcept(VideoForceDeleting::class);

    $fs = Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    $file = File::fake()->create(fake()->word().'.webm', fake()->randomDigitNotNull());
    $fsFile = $fs->putFile('', $file);

    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_PATH => $fsFile,
    ]);

    $video->forceDelete();

    static::assertFalse($fs->exists($video->path));
});
