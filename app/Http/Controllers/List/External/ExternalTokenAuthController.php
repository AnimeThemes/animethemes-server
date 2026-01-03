<?php

declare(strict_types=1);

namespace App\Http\Controllers\List\External;

use App\Enums\Models\List\ExternalProfileSite;
use App\Features\AllowExternalProfileManagement;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;
use App\Http\Requests\List\External\ExternalTokenAuthRequest;
use App\Models\List\ExternalProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class ExternalTokenAuthController extends Controller
{
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
     */
    public function index(ExternalTokenAuthRequest $request): RedirectResponse
    {
        /** @var ExternalProfileSite $site */
        $site = ExternalProfileSite::fromLocalizedName($request->validated(ExternalProfile::ATTRIBUTE_SITE));

        return $site->getAuthorizeUrl()->redirect();
    }
}
