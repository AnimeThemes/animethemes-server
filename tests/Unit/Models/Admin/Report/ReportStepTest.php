<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Admin;

use App\Enums\Models\Admin\ApprovableStatus;
use App\Enums\Models\Admin\ReportActionType;
use App\Models\Admin\Report;
use App\Models\Admin\Report\ReportStep;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Class ReportStepTest.
 */
class ReportStepTest extends TestCase
{
    use WithFaker;

    /**
     * Steps shall be nameable.
     *
     * @return void
     */
    public function testNameable(): void
    {
        $step = ReportStep::factory()
            ->for(Report::factory())
            ->createOne();

        static::assertIsString($step->getName());
    }

    /**
     * Steps shall have subtitle.
     *
     * @return void
     */
    public function testHasSubtitle(): void
    {
        $step = ReportStep::factory()
            ->for(Report::factory())
            ->createOne();

        static::assertIsString($step->getSubtitle());
    }

    /**
     * The action attribute of a step shall be cast to an ReportActionType enum instance.
     *
     * @return void
     */
    public function testCastsActionToEnum(): void
    {
        $step = ReportStep::factory()
            ->for(Report::factory())
            ->createOne();

        static::assertInstanceOf(ReportActionType::class, $step->action);
    }

    /**
     * The action attribute of a step shall be cast to an array.
     *
     * @return void
     */
    public function testCastsFieldsToArray(): void
    {
        $anime = Anime::factory()->makeOne();

        $step = ReportStep::factory()
            ->for(Report::factory())
            ->createOne([ReportStep::ATTRIBUTE_FIELDS => $anime->attributesToArray()]);

        static::assertIsArray($step->fields);
    }

    /**
     * Steps shall cast the finished_at attribute to datetime.
     *
     * @return void
     */
    public function testCastsFinishedAt(): void
    {
        $step = ReportStep::factory()
            ->for(Report::factory())
            ->createOne([ReportStep::ATTRIBUTE_FINISHED_AT => now()]);

        static::assertInstanceOf(Carbon::class, $step->finished_at);
    }

    /**
     * The status attribute of a step shall be cast to an ApprovableStatus enum instance.
     *
     * @return void
     */
    public function testCastsStatusToEnum(): void
    {
        $step = ReportStep::factory()
            ->for(Report::factory())
            ->createOne();

        static::assertInstanceOf(ApprovableStatus::class, $step->status);
    }

    /**
     * A step shall have a report attached.
     *
     * @return void
     */
    public function testReport(): void
    {
        $step = ReportStep::factory()
            ->for(Report::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $step->report());
        static::assertInstanceOf(Report::class, $step->report()->first());
    }
}
