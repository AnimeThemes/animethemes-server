<?php

declare(strict_types=1);

namespace App\Http\Controllers\List\External;

use App\Actions\Models\List\ExternalTokenCallbackAction;
use App\Features\AllowExternalProfileManagement;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;
use App\Http\Requests\List\External\ExternalTokenCallbackRequest;
use App\Models\List\ExternalProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class ExternalTokenCallbackController extends Controller
{
    public function __construct()
    {
        $this->middleware(EnabledOnlyOnLocalhost::class);
        $this->middleware(EnsureFeaturesAreActive::using(AllowExternalProfileManagement::class));
    }

    /**
     * This is the redirect URL which is set in the external provider.
     */
    public function index(ExternalTokenCallbackRequest $request): RedirectResponse
    {
        $validated = array_merge(
            $request->validated(),
            [ExternalProfile::ATTRIBUTE_USER => Auth::id()]
        );

        $action = new ExternalTokenCallbackAction();

        $profile = $action->store($validated);

        $profile->dispatchSyncJob();

        return $profile->getClientUrl()->redirect();
    }
}
