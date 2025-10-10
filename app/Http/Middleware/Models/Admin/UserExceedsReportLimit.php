<?php

declare(strict_types=1);

namespace App\Http\Middleware\Models\Admin;

use App\Constants\Config\ReportConstants;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Models\User\ApprovableStatus;
use App\Models\Auth\User;
use App\Models\User\Report;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class UserExceedsReportLimit
{
    /**
     * @param  Closure(Request): mixed  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $reportLimit = intval(Config::get(ReportConstants::MAX_REPORTS_QUALIFIED));

        /** @var User|null $user */
        $user = $request->user('sanctum');

        abort_if(intval($user?->reports->where(Report::ATTRIBUTE_STATUS, ApprovableStatus::PENDING->value)->count()) >= $reportLimit
        && blank($user?->can(SpecialPermission::BYPASS_FEATURE_FLAGS->value)), 403, "User cannot have more than '$reportLimit' outstanding reports.");

        return $next($request);
    }
}
