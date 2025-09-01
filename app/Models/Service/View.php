<?php

declare(strict_types=1);

namespace App\Models\Service;

use App\Models\Auth\User;
use CyrildeWit\EloquentViewable\View as BaseView;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * @property string|null $referer
 * @property string|null $user_agent
 * @property int|null $user_id
 * @property User|null $user
 */
class View extends BaseView
{
    final public const TABLE = 'views';

    /**
     * Perform any actions required after the model boots.
     */
    protected static function booted(): void
    {
        parent::booted();

        static::creating(function (View $view) {
            $view->user_agent = Request::userAgent();
            $view->user_id = Auth::id();
            $view->referer = Request::header('referer');
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
