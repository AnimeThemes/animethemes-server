<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Models\Admin\Report;
use App\Models\Admin\Report\ReportStep;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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

            if (Gate::forUser(Auth::user())->check('create', $model)) {
                return;
            }

            Report::makeReport(ReportStep::makeForCreate($model::class, $model->attributesToArray()));

            return false;
        });

        static::deleting(function (BaseModel $model) {

            if (Gate::forUser(Auth::user())->check('delete', $model)) {
                return;
            }

            Report::makeReport(ReportStep::makeForDelete($model));

            return false;
        });

        static::updating(function (BaseModel $model) {

            if (Gate::forUser(Auth::user())->check('update', $model)) {
                return;
            }

            Report::makeReport(ReportStep::makeForUpdate($model, $model->attributesToArray()));

            return false;
        });
    }
}
