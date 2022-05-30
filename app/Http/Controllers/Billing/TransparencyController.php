<?php

declare(strict_types=1);

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Billing\TransparencyRequest;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Spatie\RouteDiscovery\Attributes\Route;

/**
 * Class TransparencyController.
 */
class TransparencyController extends Controller
{
    /**
     * Show the transparency for the application.
     *
     * @param  TransparencyRequest  $request
     * @return View | Factory
     */
    #[Route(fullUri: 'transparency', name: 'transparency.show')]
    public function show(TransparencyRequest $request): View|Factory
    {
        return view('billing.transparency', [
            'balances' => $request->getBalances(),
            'transactions' => $request->getTransactions(),
            'filterOptions' => $request->getValidDates(),
            'selectedDate' => $request->getSelectedDate(),
        ]);
    }
}
