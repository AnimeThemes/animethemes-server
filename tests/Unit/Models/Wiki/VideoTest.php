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
use App\Models\Wiki\Audio;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use App\Pivots\Wiki\EntryVideo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

pest()->use(WithFaker::class);

test('casts overlap to enum', function (): void {
    $video = Video::factory()->createOne();

    $overlap = $video->overlap;

    expect($overlap)->toBeInstanceOf(VideoOverlap::class);
});

test('casts source to enum', function (): void {
    $video = Video::factory()->createOne();

    $source = $video->source;

    expect($source)->toBeInstanceOf(VideoSource::class);
});

test('searchable as', function (): void {
    $video = Video::factory()->createOne();

    expect($video->searchableAs())->toBeString();
});

test('to searchable array', function (): void {
    $video = Video::factory()->createOne();

    expect($video->toSearchableArray())->toBeArray();
});

test('nameable', function (): void {
    $video = Video::factory()->createOne();

    expect($video->getName())->toBeString();
});

test('has subtitle', function (): void {
    $video = Video::factory()->createOne();

    expect($video->getSubtitle())->toBeString();
});

test('appends tags', function (): void {
    $video = Video::factory()->createOne();

    expect($video)->toHaveKey(Video::ATTRIBUTE_TAGS);
});

test('nc tag', function (): void {
    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_NC => true,
    ]);

    expect($video->tags)->toContain('NC');
});

test('no nc tag', function (): void {
    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_NC => false,
    ]);

    expect($video->tags)->not->toContain('NC');
});

test('dvd tag', function (): void {
    $source = VideoSource::DVD;

    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_SOURCE => $source->value,
    ]);

    expect($video->tags)->toContain($source->localize());
});

test('bd tag', function (): void {
    $source = VideoSource::BD;

    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_SOURCE => $source->value,
    ]);

    expect($video->tags)->toContain($source->localize());
});

test('other source tag', function (): void {
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

    expect($video->tags)->not->toContain($source->localize());
});

test('resolution tag', function (): void {
    $video = Video::factory()->createOne();

    expect($video->tags)->toContain(strval($video->resolution));
});

test('no720 resolution tag', function (): void {
    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_RESOLUTION => 720,
    ]);

    expect($video->tags)->not->toContain(strval($video->resolution));
});

test('subbed tag', function (): void {
    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_SUBBED => true,
    ]);

    expect($video->tags)->toContain('Subbed');
    expect($video->tags)->not->toContain('Lyrics');
});

test('lyrics tag', function (): void {
    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_SUBBED => false,
        Video::ATTRIBUTE_LYRICS => true,
    ]);

    expect($video->tags)->not->toContain('Subbed');
    expect($video->tags)->toContain('Lyrics');
});

test('source priority', function (array $a, array $b): void {
    $first = Video::factory()->createOne($a);

    $second = Video::factory()->createOne($b);

    expect($second->getAttribute(Video::ATTRIBUTE_PRIORITY))->toBeGreaterThan($first->getAttribute(Video::ATTRIBUTE_PRIORITY));
})->with('priorityProvider');
test('entries', function (): void {
    $entryCount = fake()->randomDigitNotNull();

    $video = Video::factory()
        ->has(Entry::factory()->for(Theme::factory()->for(Anime::factory()))->count($entryCount))
        ->createOne();

    expect($video->entries())->toBeInstanceOf(BelongsToMany::class);
    expect($video->entries()->count())->toEqual($entryCount);
    expect($video->entries()->first())->toBeInstanceOf(Entry::class);
    expect($video->entries()->getPivotClass())->toEqual(EntryVideo::class);
});

test('audio', function (): void {
    $video = Video::factory()
        ->for(Audio::factory())
        ->createOne();

    expect($video->audio())->toBeInstanceOf(BelongsTo::class);
    expect($video->audio()->first())->toBeInstanceOf(Audio::class);
});

test('tracks public', function (): void {
    $trackCount = fake()->randomDigitNotNull();

    $playlist = Playlist::factory()->createOne([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC]);
    $video = Video::factory()
        ->has(PlaylistTrack::factory()->for($playlist)->count($trackCount), Video::RELATION_TRACKS)
        ->createOne();

    expect($video->tracks())->toBeInstanceOf(HasMany::class);
    expect($video->tracks()->count())->toEqual($trackCount);
    expect($video->tracks()->first())->toBeInstanceOf(PlaylistTrack::class);
});

test('tracks not public', function (): void {
    $trackCount = fake()->randomDigitNotNull();

    $visibility = Arr::random([PlaylistVisibility::PRIVATE, PlaylistVisibility::UNLISTED]);
    $playlist = Playlist::factory()->createOne([Playlist::ATTRIBUTE_VISIBILITY => $visibility]);
    $video = Video::factory()
        ->has(PlaylistTrack::factory()->for($playlist)->count($trackCount), Video::RELATION_TRACKS)
        ->createOne();

    expect($video->tracks())->toBeInstanceOf(HasMany::class);
    expect($video->tracks()->count())->not->toEqual($trackCount);
});

test('script', function (): void {
    $video = Video::factory()
        ->has(VideoScript::factory(), Video::RELATION_SCRIPT)
        ->createOne();

    expect($video->videoscript())->toBeInstanceOf(HasOne::class);
    expect($video->videoscript()->first())->toBeInstanceOf(VideoScript::class);
});
/**
 * Provider for source priority testing.
 *
 * @return array
 */
dataset('priorityProvider', fn (): array => [
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
]);

test('video storage deletion', function (): void {
    $fs = Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    $file = File::fake()->create(fake()->word().'.webm', fake()->randomDigitNotNull());
    $fsFile = $fs->putFile('', $file);

    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_PATH => $fsFile,
    ]);

    $video->delete();

    expect($fs->exists($video->path))->toBeTrue();
});

test('video storage force deletion', function (): void {
    Event::fakeExcept(VideoForceDeleting::class);

    $fs = Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    $file = File::fake()->create(fake()->word().'.webm', fake()->randomDigitNotNull());
    $fsFile = $fs->putFile('', $file);

    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_PATH => $fsFile,
    ]);

    $video->forceDelete();

    expect($fs->exists($video->path))->toBeFalse();
});
