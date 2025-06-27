<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Repositories;

use App\Actions\Repositories\ReconcileResults;
use App\Enums\Actions\ActionStatus;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

/**
 * Class ReconcileResultsTest.
 */
class ReconcileResultsTest extends TestCase
{
    /**
     * The Reconcile Results shall always return true.
     *
     * @return void
     */
    public function test_default(): void
    {
        $reconcileResults = new class extends ReconcileResults
        {
            /**
             * Get the model of the reconciliation results.
             *
             * @return class-string<Model>
             */
            protected function model(): string
            {
                return Model::class;
            }
        };

        static::assertTrue($reconcileResults->getStatus() === ActionStatus::PASSED);
    }
}
