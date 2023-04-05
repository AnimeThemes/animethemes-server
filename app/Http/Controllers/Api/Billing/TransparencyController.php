<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Billing\TransparencyRequest;
use App\Http\Resources\Billing\Resource\TransparencyResource;

/**
 * Class TransparencyController.
 */
class TransparencyController extends Controller
{
    /**
     * Show the transparency for the application.
     *
     * @param  TransparencyRequest  $request
     * @return TransparencyResource
     */
    public function show(TransparencyRequest $request): TransparencyResource
    {
        return new TransparencyResource(
            $request->getSelectedDate(),
            $request->getValidDates(),
            $request->getBalances(),
            $request->getTransactions()
        );
    }
}
