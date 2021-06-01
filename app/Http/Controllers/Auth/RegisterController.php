<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Concerns\Fortify\PasswordValidationRules;
use App\Enums\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Class RegisterController
 * @package App\Http\Controllers\Auth
 */
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use PasswordValidationRules;
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @return string
     */
    public function redirectPath(): string
    {
        return route('dashboard');
    }

    /**
     * Show the application registration form.
     *
     * @return View
     */
    public function showRegistrationForm(): View
    {
        $token = request('token');
        $invitation = Invitation::where('token', $token)->firstOrFail();

        return view('auth.register', [
            'invitation' => $invitation,
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     * @throws ValidationException
     */
    public function register(Request $request): JsonResponse|RedirectResponse
    {
        $invitation = Invitation::where('token', $request->input('token'))->firstOrFail();

        $data = array_merge($request->all(), [
            'name' => $invitation->name,
            'email' => $invitation->email,
        ]);

        $this->validator($data)->validate();

        event(new Registered($user = $this->create($data)));

        $invitation->status = InvitationStatus::CLOSED;
        $invitation->save();

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
                    ? new JsonResponse([], 201)
                    : redirect($this->redirectPath());
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:192'],
            'email' => ['required', 'string', 'email', 'max:192', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => ['required'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return User
     */
    protected function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
