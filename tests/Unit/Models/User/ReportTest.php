<?php

declare(strict_types=1);

namespace Tests\Unit\Models\User;

use App\Enums\Models\User\ApprovableStatus;
use App\Models\Auth\User;
use App\Models\User\Report;
use App\Models\User\Report\ReportStep;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Class ReportTest.
 */
class ReportTest extends TestCase
{
    use WithFaker;

    /**
     * Reports shall be nameable.
     *
     * @return void
     */
    public function test_nameable(): void
    {
        $report = Report::factory()->createOne();

        static::assertIsString($report->getName());
    }

    /**
     * Reports shall have subtitle.
     *
     * @return void
     */
    public function test_has_subtitle(): void
    {
        $report = Report::factory()->createOne();

        static::assertIsString($report->getSubtitle());
    }

    /**
     * Reports shall cast the finished_at attribute to datetime.
     *
     * @return void
     */
    public function test_casts_finished_at(): void
    {
        $report = Report::factory()->createOne([Report::ATTRIBUTE_FINISHED_AT => now()]);

        static::assertInstanceOf(Carbon::class, $report->finished_at);
    }

    /**
     * The status attribute of a report shall be cast to an ApprovableStatus enum instance.
     *
     * @return void
     */
    public function test_casts_status_to_enum(): void
    {
        $report = Report::factory()->createOne();

        static::assertInstanceOf(ApprovableStatus::class, $report->status);
    }

    /**
     * Reports shall have a one-to-many relationship with the type ReportStep.
     *
     * @return void
     */
    public function test_steps(): void
    {
        $stepsCount = $this->faker->randomDigitNotNull();

        $report = Report::factory()->createOne();

        ReportStep::factory()
            ->for($report)
            ->count($stepsCount)
            ->create();

        static::assertInstanceOf(HasMany::class, $report->steps());
        static::assertEquals($stepsCount, $report->steps()->count());
        static::assertInstanceOf(ReportStep::class, $report->steps()->first());
    }

    /**
     * A report shall have a user attached.
     *
     * @return void
     */
    public function test_user(): void
    {
        $report = Report::factory()
            ->for(User::factory(), Report::RELATION_USER)
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $report->user());
        static::assertInstanceOf(User::class, $report->user()->first());
    }

    /**
     * A report shall have a moderator attached.
     *
     * @return void
     */
    public function test_moderator(): void
    {
        $report = Report::factory()
            ->for(User::factory(), Report::RELATION_MODERATOR)
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $report->moderator());
        static::assertInstanceOf(User::class, $report->moderator()->first());
    }
}
