<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\ConfirmTwoFactorAuthRule;
use Illuminate\Http\Request;

class TwoFactorAuthenticationController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function create(Request $request)
    {
        $secret = $request->user()->createTwoFactorAuth();

        return view('auth.2fa', [
            'qrCode'   => $secret->toQr(),
            'asString' => $secret->toString(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string', new ConfirmTwoFactorAuthRule($request->user())],
        ]);

        return redirect()->to(url('nova/resources/users/' . $request->user()->id));
    }
}
