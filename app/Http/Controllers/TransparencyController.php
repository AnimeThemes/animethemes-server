<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransparencyRequest;

class TransparencyController extends Controller
{
    /**
     * Show the transparency for the application.
     *
     * @param \App\Http\Requests\TransparencyRequest $request
     * @return \Illuminate\View\View
     */
    public function show(TransparencyRequest $request)
    {
        return view('transparency', [
            'balances' => $request->getBalances(),
            'transactions' => $request->getTransactions(),
            'filterOptions' => $request->getValidDates(),
            'selectedDate' => $request->getSelectedDate(),
        ]);
    }
}
