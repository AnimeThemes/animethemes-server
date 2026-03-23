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
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class ExternalTokenAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware(EnabledOnlyOnLocalhost::class);
        $this->middleware(EnsureFeaturesAreActive::using(AllowExternalProfileManagement::class));
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
