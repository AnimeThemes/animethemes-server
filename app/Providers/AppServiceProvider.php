<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Middleware\Models\RecordView;
use Database\Seeders\Auth\Permission\PermissionSeeder;
use Database\Seeders\Auth\Role\AdminSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::automaticallyEagerLoadRelationships();

        Model::preventLazyLoading();

        DB::prohibitDestructiveCommands(app()->isProduction());

        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) {
            $class = get_class($model);

            Log::error("Attempted to lazy load '$relation' on model '$class'.", [
                'method' => request()->method(),
                'full-url' => request()->fullUrl(),
                'ip' => request()->ip(),
                'headers' => request()->headers->all(),
            ]);
        });

        Model::preventsAccessingMissingAttributes();

        Model::handleMissingAttributeViolationUsing(function (Model $model, string $key) {
            $class = get_class($model);

            Log::error("Attribute '$key' does not exist or was not retrieved for model '$class'", [
                'method' => request()->method(),
                'full-url' => request()->fullUrl(),
                'ip' => request()->ip(),
                'headers' => request()->headers->all(),
            ]);
        });

        EnsureFeaturesAreActive::whenInactive(fn (Request $request, array $features) => new Response(status: 403));

        ParallelTesting::setUpTestDatabase(function (string $database, int $token) {
            Artisan::call('db:seed', ['--class' => PermissionSeeder::class]);
            Artisan::call('db:seed', ['--class' => AdminSeeder::class]);
        });

        DB::listen(function (QueryExecuted $query) {
            if (app()->isLocal()) {
                Log::debug($query->toRawSql());
            }
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RecordView::class);
    }
}
