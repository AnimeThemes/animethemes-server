<?php

declare(strict_types=1);

namespace App\Http\Middleware\Models\Admin;

use App\Constants\Config\SubmissionConstants;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Models\User\SubmissionStatus;
use App\Models\Auth\User;
use App\Models\User\Submission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class UserExceedsSubmissionLimit
{
    /**
     * @param  Closure(Request): mixed  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $submissionLimit = intval(Config::get(SubmissionConstants::MAX_SUBMISSIONS_QUALIFIED));

        /** @var User|null $user */
        $user = $request->user('sanctum');

        abort_if(intval($user?->submissions->where(Submission::ATTRIBUTE_STATUS, SubmissionStatus::PENDING->value)->count()) >= $submissionLimit
        && blank($user?->can(SpecialPermission::BYPASS_FEATURE_FLAGS->value)), 403, "User cannot have more than '$submissionLimit' outstanding submissions.");

        return $next($request);
    }
}
