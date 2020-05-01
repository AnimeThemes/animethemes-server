<?php

namespace Tests\Unit;

use App\Console\Commands\VideoReconcileCommand;
use PHPUnit\Framework\TestCase;

class VideoReconcileTest extends TestCase
{

    public function testNoResults() {
        $command = new VideoReconcileCommand;
        $this->assertFalse($command->hasResults());
    }

    public function testNoResultsVideoCreated() {
        $command = new VideoReconcileCommand;
        $command->created++;
        $this->assertTrue($command->hasResults());
    }

    public function testNoResultsVideoCreatedFailed() {
        $command = new VideoReconcileCommand;
        $command->created_failed++;
        $this->assertTrue($command->hasResults());
    }

    public function testNoResultsVideoDeleted() {
        $command = new VideoReconcileCommand;
        $command->deleted++;
        $this->assertTrue($command->hasResults());
    }

    public function testNoResultsVideoDeletedFailed() {
        $command = new VideoReconcileCommand;
        $command->deleted_failed++;
        $this->assertTrue($command->hasResults());
    }

    public function testHasChanges() {
        $command = new VideoReconcileCommand;
        $this->assertFalse($command->hasChanges());
    }

    public function testHasChangesFailed() {
        $command = new VideoReconcileCommand;
        $command->deleted_failed++;
        $command->created_failed++;
        $this->assertFalse($command->hasChanges());
    }

    public function testHasChangesCreated() {
        $command = new VideoReconcileCommand;
        $command->created++;
        $this->assertTrue($command->hasChanges());
    }

    public function testHasChangesDeleted() {
        $command = new VideoReconcileCommand;
        $command->deleted++;
        $this->assertTrue($command->hasChanges());
    }

    public function testHasFailures() {
        $command = new VideoReconcileCommand;
        $this->assertFalse($command->hasFailures());
    }

    public function testHasFailuresSuccess() {
        $command = new VideoReconcileCommand;
        $command->created++;
        $command->deleted++;
        $this->assertFalse($command->hasFailures());
    }

    public function testHasFailuresCreated() {
        $command = new VideoReconcileCommand;
        $command->created_failed++;
        $this->assertTrue($command->hasFailures());
    }

    public function testHasFailuresDeleted() {
        $command = new VideoReconcileCommand;
        $command->deleted_failed++;
        $this->assertTrue($command->hasFailures());
    }
}
