<?php

declare(strict_types=1);

namespace App\Http\Controllers\List\External;

use App\Enums\Models\List\ExternalProfileSite;
use App\Features\AllowExternalProfileManagement;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;
use App\Models\List\ExternalProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

/**
 * Class ExternalTokenAuthController.
 */
class ExternalTokenAuthController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $isExternalProfileManagementAllowed = Str::of(EnsureFeaturesAreActive::class)
            ->append(':')
            ->append(AllowExternalProfileManagement::class)
            ->__toString();

        $this->middleware(EnabledOnlyOnLocalhost::class);
        $this->middleware($isExternalProfileManagementAllowed);
    }

    /**
     * This will redirect the user to the appropriate auth service.
     *
     * @param  Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function index(Request $request): RedirectResponse|JsonResponse
    {
        $site = Arr::get($request->all(), ExternalProfile::ATTRIBUTE_SITE);
        $profileSite = ExternalProfileSite::fromLocalizedName($site);

        if ($profileSite instanceof ExternalProfileSite) {
            $link = $profileSite->getAuthorizeUrl();

            if ($link !== null) {
                return Redirect::to($link);
            }
        }

        return new JsonResponse([
            'error' => 'invalid site',
        ], 400);
    }
}
