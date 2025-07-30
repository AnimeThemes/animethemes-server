<?php

declare(strict_types=1);

use App\Actions\Repositories\ReconcileResults;
use App\Enums\Actions\ActionStatus;
use Illuminate\Database\Eloquent\Model;

test('default', function () {
    $reconcileResults = new class extends ReconcileResults
    {
        /**
         * Get the model of the reconciliation results.
         *
         * @return class-string<Model>
         */
        public function model(): string
        {
            return Model::class;
        }
    };

    static::assertTrue($reconcileResults->getStatus() === ActionStatus::PASSED);
});
