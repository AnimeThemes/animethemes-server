<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List;

use App\Features\AllowExternalProfileManagement;
use App\Http\Api\Schema\Schema;
use App\Http\Controllers\Api\BaseController;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;
use App\Http\Requests\Api\ShowRequest;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Str;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

/**
 * Class SyncExternalProfileController.
 */
class SyncExternalProfileController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(ExternalProfile::class, 'externalprofile');

        $isExternalProfileManagementAllowed = Str::of(EnsureFeaturesAreActive::class)
            ->append(':')
            ->append(AllowExternalProfileManagement::class)
            ->__toString();

        $this->middleware(EnabledOnlyOnLocalhost::class);
        $this->middleware($isExternalProfileManagementAllowed)->except(['show']);
    }

    /**
     * Display the current progress status of the sync.
     *
     * @param  ShowRequest  $request
     * @param  ExternalProfile  $externalprofile
     */
    public function show(ShowRequest $request, ExternalProfile $externalprofile)
    {
        // TODO
    }

    /**
     * Start a new sync job.
     */
    public function store()
    {
        // TODO
    }

    /**
     * Get the underlying schema.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new Schema(); // TODO
    }
}
