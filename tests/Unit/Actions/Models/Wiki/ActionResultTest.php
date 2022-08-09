<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Models\Wiki;

use App\Actions\Models\ActionResult;
use App\Enums\Actions\ActionStatus;
use Tests\TestCase;

/**
 * Class ActionResultTest.
 */
class ActionResultTest extends TestCase
{
    /**
     * The Action Result has failed if the status is Failed.
     *
     * @return void
     */
    public function testHasFailed(): void
    {
        $result = new ActionResult(ActionStatus::FAILED());

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Action Result has not failed if the status is not Failed.
     *
     * @return void
     */
    public function testHasNotFailed(): void
    {
        $status = null;

        while ($status === null) {
            $statusCandidate = ActionStatus::getRandomInstance();
            if (ActionStatus::FAILED()->isNot($statusCandidate)) {
                $status = $statusCandidate;
            }
        }

        $result = new ActionResult($status);

        static::assertFalse($result->hasFailed());
    }
}
