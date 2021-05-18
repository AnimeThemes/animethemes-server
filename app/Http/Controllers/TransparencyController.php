<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Transaction;
use Carbon\Carbon;

class TransparencyController extends Controller
{
    /**
     * Show the transparency for the application.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $balances = Balance::whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->orderBy('usage', 'desc')
            ->get();

        $transactions = Transaction::whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->orderBy('date', 'desc')
            ->get();

        return view('transparency', [
            'balances' => $balances,
            'transactions' => $transactions,
        ]);
    }
}
