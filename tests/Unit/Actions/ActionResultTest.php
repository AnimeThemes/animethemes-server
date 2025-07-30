<?php

declare(strict_types=1);

use App\Actions\ActionResult;
use App\Enums\Actions\ActionStatus;
use Illuminate\Support\Arr;

test('has failed', function () {
    $result = new ActionResult(ActionStatus::FAILED);

    $this->assertTrue($result->hasFailed());
});

test('has not failed', function () {
    $status = null;

    while ($status === null) {
        $statusCandidate = Arr::random(ActionStatus::cases());
        if ($statusCandidate !== ActionStatus::FAILED) {
            $status = $statusCandidate;
        }
    }

    $result = new ActionResult($status);

    $this->assertFalse($result->hasFailed());
});
