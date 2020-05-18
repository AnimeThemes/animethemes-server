<?php

namespace Tests\Unit;

use Mockery;
use App\Console\Commands\VideoReconcileCommand;
use App\Models\Video;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class VideoReconcileTest extends TestCase
{

    protected $command;
    protected $video_a;
    protected $video_b;

    protected function setUp(): void {
        parent::setUp();
        $this->command = new VideoReconcileCommand;
        $this->video_a = Mockery::mock(Video::class)->makePartial();
        $this->video_b = Mockery::mock(Video::class)->makePartial();
    }

    public function testNoResults() {
        $this->assertFalse($this->command->hasResults());
    }

    public function testNoResultsVideoCreated() {
        $this->command->created++;
        $this->assertTrue($this->command->hasResults());
    }

    public function testNoResultsVideoCreatedFailed() {
        $this->command->created_failed++;
        $this->assertTrue($this->command->hasResults());
    }

    public function testNoResultsVideoDeleted() {
        $this->command->deleted++;
        $this->assertTrue($this->command->hasResults());
    }

    public function testNoResultsVideoDeletedFailed() {
        $this->command->deleted_failed++;
        $this->assertTrue($this->command->hasResults());
    }

    public function testHasChanges() {
        $this->assertFalse($this->command->hasChanges());
    }

    public function testHasChangesFailed() {
        $this->command->deleted_failed++;
        $this->command->created_failed++;
        $this->assertFalse($this->command->hasChanges());
    }

    public function testHasChangesCreated() {
        $this->command->created++;
        $this->assertTrue($this->command->hasChanges());
    }

    public function testHasChangesDeleted() {
        $this->command->deleted++;
        $this->assertTrue($this->command->hasChanges());
    }

    public function testHasFailures() {
        $this->assertFalse($this->command->hasFailures());
    }

    public function testHasFailuresSuccess() {
        $this->command->created++;
        $this->command->deleted++;
        $this->assertFalse($this->command->hasFailures());
    }

    public function testHasFailuresCreated() {
        $this->command->created_failed++;
        $this->assertTrue($this->command->hasFailures());
    }

    public function testHasFailuresDeleted() {
        $this->command->deleted_failed++;
        $this->assertTrue($this->command->hasFailures());
    }

    public function testReconciliationString() {
        $this->assertEquals(VideoReconcileCommand::reconciliationString($this->video_a), "basename:{$this->video_a->basename},filename:{$this->video_a->filename},path:{$this->video_a->path}");
    }

    public function testCompareVideosEquals() {
        $this->assertEquals(VideoReconcileCommand::compareVideos($this->video_a, $this->video_a), 0);
    }

    public function testCompareVideosNotEquals() {
        $this->assertEquals(VideoReconcileCommand::compareVideos($this->video_a, $this->video_b), 0);
    }

    public function tearDown(): void {
        parent::tearDown();
        Mockery::close();
    }
}
