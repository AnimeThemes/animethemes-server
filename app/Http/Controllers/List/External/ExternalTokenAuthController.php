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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
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
        $validated = array_merge(
            $request->all(),
            [ExternalProfile::ATTRIBUTE_USER => Auth::id()]
        );

        $site = Arr::get($validated, ExternalProfile::ATTRIBUTE_SITE);
        $profileSite = ExternalProfileSite::fromLocalizedName($site);

        if ($profileSite instanceof ExternalProfileSite) {
            $link = $this->getRedirectLink($profileSite);

            if ($link !== null) {
                return Redirect::to($link);
            }
        }

        return new JsonResponse([
            'error' => 'invalid site',
        ], 400);
    }

    /**
     * Get the link of the external site to authenticate the user.
     *
     * @param  ExternalProfileSite  $site
     * @return string
     */
    private function getRedirectLink(ExternalProfileSite $site): ?string
    {
        switch ($site) {
            case ExternalProfileSite::KITSU:
                return null;
            case ExternalProfileSite::MAL:
                return null;
            case ExternalProfileSite::ANILIST:
                $query = [
                    'client_id' => Config::get('services.anilist.client_id'),
                    'redirect_uri' => Config::get('services.anilist.redirect_uri'),
                    'response_type' => 'code',
                ];

                return 'https://anilist.co/api/v2/oauth/authorize?' . http_build_query($query);
            default:
                return null;
        }
    }
}
