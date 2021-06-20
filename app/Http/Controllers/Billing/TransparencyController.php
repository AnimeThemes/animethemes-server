<?php

declare(strict_types=1);

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransparencyRequest;
use Illuminate\View\View;

/**
 * Class TransparencyController.
 */
class TransparencyController extends Controller
{
    /**
     * Show the transparency for the application.
     *
     * @param TransparencyRequest $request
     * @return View
     */
    public function show(TransparencyRequest $request): View
    {
        return view('billing.transparency', [
            'balances' => $request->getBalances(),
            'transactions' => $request->getTransactions(),
            'filterOptions' => $request->getValidDates(),
            'selectedDate' => $request->getSelectedDate(),
        ]);
    }
}
