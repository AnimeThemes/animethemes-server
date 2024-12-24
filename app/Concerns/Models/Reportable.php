<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Enums\Models\Admin\ApprovableStatus;
use App\Models\Admin\Report;
use App\Models\Admin\Report\ReportStep;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

/**
 * Trait Reportable.
 */
trait Reportable
{
    /**
     * Get the reports made to the model.
     *
     * @return MorphMany
     */
    public function reportsteps(): MorphMany
    {
        return $this->morphMany(ReportStep::class, ReportStep::ATTRIBUTE_ACTIONABLE);
    }

    /**
     * Bootstrap the model.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (BaseModel $model) {

            $report = Report::query()->create([
                Report::ATTRIBUTE_USER => Auth::id(),
                Report::ATTRIBUTE_STATUS => ApprovableStatus::PENDING->value,
                Report::ATTRIBUTE_NOTES => 'notes',
            ]);

            ReportStep::makeForCreate($model::class, $model->attributesToArray(), report: $report);

            return false;
        });

        static::deleting(function (BaseModel $model) {
            $report = Report::query()->create([
                Report::ATTRIBUTE_USER => Auth::id(),
                Report::ATTRIBUTE_STATUS => ApprovableStatus::PENDING->value,
                Report::ATTRIBUTE_NOTES => 'notes',
            ]);

            ReportStep::makeForDelete($model, report: $report);

            return false;
        });

        static::updating(function (BaseModel $model) {
            $report = Report::query()->create([
                Report::ATTRIBUTE_USER => Auth::id(),
                Report::ATTRIBUTE_STATUS => ApprovableStatus::PENDING->value,
                Report::ATTRIBUTE_NOTES => 'notes',
            ]);

            ReportStep::makeForUpdate($model, $model->attributesToArray(), report: $report);

            return false;
        });
    }
}
