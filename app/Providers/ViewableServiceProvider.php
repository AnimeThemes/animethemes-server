<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Service\View;
use CyrildeWit\EloquentViewable\Contracts\View as ViewContract;
use CyrildeWit\EloquentViewable\EloquentViewableServiceProvider;

/**
 * Class ViewableServiceProvider.
 */
class ViewableServiceProvider extends EloquentViewableServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register(): void
    {
        parent::register();

        $this->app->bind(ViewContract::class, View::class);
    }
}