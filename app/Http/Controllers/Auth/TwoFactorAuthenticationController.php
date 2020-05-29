<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RecoveryCodesMail;
use App\Rules\ConfirmTwoFactorAuthRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TwoFactorAuthenticationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
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

        Mail::to($request->user()->email)->queue(new RecoveryCodesMail($request->user()));

        return redirect()->to(url('nova/resources/users/' . $request->user()->id));
    }
}
