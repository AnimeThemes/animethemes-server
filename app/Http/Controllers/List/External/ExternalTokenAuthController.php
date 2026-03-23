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
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

#[Middleware(EnabledOnlyOnLocalhost::class)]
#[Middleware(EnsureFeaturesAreActive::class.':'.AllowExternalProfileManagement::class)]
class ExternalTokenAuthController extends Controller
{
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
