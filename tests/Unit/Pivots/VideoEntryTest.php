<?php

namespace Tests\Unit\Pivots;

use App\Models\Anime;
use App\Models\Video;
use App\Models\Entry;
use App\Models\Theme;
use App\Pivots\VideoEntry;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VideoEntryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A VideoEntry shall belong to a Video.
     *
     * @return void
     */
    public function testVideo()
    {
        $video_entry = VideoEntry::factory()
            ->for(Video::factory())
            ->for(Entry::factory()->for(Theme::factory()->for(Anime::factory())))
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $video_entry->video());
        $this->assertInstanceOf(Video::class, $video_entry->video()->first());
    }

    /**
     * A VideoEntry shall belong to an Entry.
     *
     * @return void
     */
    public function testEntry()
    {
        $video_entry = VideoEntry::factory()
            ->for(Video::factory())
            ->for(Entry::factory()->for(Theme::factory()->for(Anime::factory())))
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $video_entry->entry());
        $this->assertInstanceOf(Entry::class, $video_entry->entry()->first());
    }
}
