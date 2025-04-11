<?php

declare(strict_types=1);

namespace App\Http\Middleware\Models\Admin;

use App\Constants\Config\ReportConstants;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Models\User\ApprovableStatus;
use App\Models\User\Report;
use App\Models\Auth\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Class UserExceedsReportLimit.
 */
class UserExceedsReportLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request): mixed  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $reportLimit = intval(Config::get(ReportConstants::MAX_REPORTS_QUALIFIED));

        /** @var User|null $user */
        $user = $request->user('sanctum');

        if (
            intval($user?->reports->where(Report::ATTRIBUTE_STATUS, ApprovableStatus::PENDING->value)->count()) >= $reportLimit
            && empty($user?->can(SpecialPermission::BYPASS_FEATURE_FLAGS->value))
        ) {
            abort(403, "User cannot have more than '$reportLimit' outstanding reports.");
        }

        return $next($request);
    }
}
