<?php

declare(strict_types=1);

namespace App\Models\Service;

use App\Models\Auth\User;
use CyrildeWit\EloquentViewable\View as BaseView;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class View.
 *
 * @property string|null $referer
 * @property string|null $user_agent
 * @property int|null $user_id
 * @property User|null $user
 */
class View extends BaseView
{
    final public const TABLE = 'views';

    /**
     * Bootstrap the model.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (View $view) {
            $view->user_agent = Request::userAgent();
            $view->user_id = Auth::id();
            $view->referer = Request::header('referer');
        });
    }

    /**
     * Get the user related to the view.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
