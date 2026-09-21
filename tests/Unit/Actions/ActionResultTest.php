<?php

declare(strict_types=1);

use App\Actions\ActionResult;
use App\Enums\Actions\ActionStatus;
use Illuminate\Support\Arr;

test('has failed', function (): void {
    $result = new ActionResult(ActionStatus::FAILED);

    expect($result->hasFailed())->toBeTrue();
});

test('has not failed', function (): void {
    $status = null;

    while ($status === null) {
        $statusCandidate = Arr::random(ActionStatus::cases());
        if ($statusCandidate !== ActionStatus::FAILED) {
            $status = $statusCandidate;
        }
    }

    $result = new ActionResult($status);

    expect($result->hasFailed())->toBeFalse();
});
