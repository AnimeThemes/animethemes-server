<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Middleware\Models\RecordView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

/**
 * Class AppServiceProvider.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Model::preventLazyLoading();

        DB::prohibitDestructiveCommands(app()->isProduction());

        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) {
            $class = get_class($model);

            Log::error("Attempted to lazy load '$relation' on model '$class'");
        });

        Model::preventsAccessingMissingAttributes();

        Model::handleMissingAttributeViolationUsing(function (Model $model, string $key) {
            $class = get_class($model);

            Log::error("Attribute '$key' does not exist or was not retrieved for model '$class'");
        });

        EnsureFeaturesAreActive::whenInactive(fn (Request $request, array $features) => new Response(status: 403));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(RecordView::class);
    }
}
