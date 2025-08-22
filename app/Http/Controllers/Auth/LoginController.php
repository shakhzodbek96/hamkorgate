<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\TelegramBotController;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string|max:255',
            'password' => 'required|string|max:255|min:6',
        ]);
    }

    protected function credentials(Request $request)
    {
        if (is_numeric($request->get('email'))) {
            return [
                'phone' => "+998".substr(preg_replace('/[^0-9]/', '', $request->get('email')), -9),
                'password' => $request->get('password')
            ];
        }
        return ['email' => $request->get('email'), 'password' => $request->get('password')];
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $user = User::where('email', $request->email)->first();

        if ($user && password_verify($request->password, $user->password)) {
            if (env('TELEGRAM_VERIFICATION', false)) {
                if (!$user->chat_id) {
                    $telegram_bot_username = env('TELEGRAM_BOT_USERNAME', 'bot_username');
                    $telegram_url = "https://t.me/{$telegram_bot_username}?start=" . base64_encode(json_encode(['id' => $user->id, 'email' => $user->email]));
                    return redirect($telegram_url);
                }

                if (!Session::has('otp_code')) {
                    $otpConfig = $user->otp_config;
                    if (is_null($otpConfig['otp_login_at']) || $otpConfig['otp_login_at'] < now()->subDays(7)) {
                        $otp_code = rand(1000, 9999);

                        $user->otp_config = [
                            'otp_code' => $otp_code,
                            'otp_sent_at' => now(),
                            'otp_expires_at' => now()->addMinutes(5),
                            'otp_login_attempts' => 0,
                            'otp_login_at' => now(),
                            'otp_login_ip' => $request->ip(),
                            'one_time_token' => null,
                            'one_time_token_expires_at' => null
                        ];
                        $user->save();

                        $telegramBotController = new TelegramBotController();
                        $telegramBotController->sendMessage($user->chat_id, "Ваш код подтверждения для входа Autopay: $otp_code");

                        Session::put([
                            'otp_code' => $otp_code,
                            'otp_expires_at' => $user->otp_config['otp_expires_at'],
                            'email' => $request->email,
                            'password' => $request->password
                        ]);

                        return redirect()->route('login')->withInput();
                    }
                }

                if (Session::has('otp_code')) {
                    $otp_code = $request->otp_code;
                    $otp_config = $user->otp_config;

                    if ($otp_code && $otp_config['otp_code'] == $otp_code) {
                        Session::forget([
                            'otp_code',
                            'otp_expires_at',
                            'email',
                            'password'
                        ]);

                        if ($otp_config['otp_expires_at'] < now()) {
                            $user->otp_config = [
                                'otp_code' => null,
                                'otp_sent_at' => null,
                                'otp_expires_at' => null,
                                'otp_login_attempts' => 0,
                                'otp_login_at' => null,
                                'otp_login_ip' => null,
                                'one_time_token' => null,
                                'one_time_token_expires_at' => null
                            ];
                            $user->save();
                            return redirect()->route('login')->with('error', 'Срок кода OTP истек.');
                        }

                        $otp_config = [
                            'otp_code' => null,
                            'otp_sent_at' => null,
                            'otp_expires_at' => null,
                            'otp_login_attempts' => 0,
                            'otp_login_at' => now(),
                            'otp_login_ip' => $request->ip(),
                            'one_time_token' => null,
                            'one_time_token_expires_at' => null
                        ];

                        $user->otp_config = $otp_config;
                        $user->save();
                    } else {
                        $otp_config['otp_login_attempts']++;
                        $user->otp_config = $otp_config;
                        $user->save();

                        if ($otp_config['otp_login_attempts'] >= 3) {
                            Session::forget('otp_code');

                            $user->otp_config = [
                                'otp_code' => null,
                                'otp_sent_at' => null,
                                'otp_expires_at' => null,
                                'otp_login_attempts' => 0,
                                'otp_login_at' => null,
                                'otp_login_ip' => null,
                                'one_time_token' => null,
                                'one_time_token_expires_at' => null
                            ];
                            $user->save();

                            return redirect()->route('login')->withInput()->with('error', 'Вы превысили лимит попыток входа.');
                        }

                        return redirect()->route('login')->withInput()->with('error', 'Неверный код подтверждения. У вас осталось ' . $otp_config['otp_login_attempts'] . ' из 3');
                    }
                }
            }

            if (method_exists($this, 'hasTooManyLoginAttempts') && $this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);
                return $this->sendLockoutResponse($request);
            }

            if ($this->attemptLogin($request)) {
                if ($request->hasSession()) {
                    $request->session()->put('auth.password_confirmed_at', time());
                }

                if (Auth::user()->partner_id > 0)
                    Session::put('partner_id', Auth::user()->partner_id);

                Session::put('is_admin', Auth::user()->is_admin);
                return $this->sendLoginResponse($request);
            }

            $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);
        }

        return $this->sendFailedLoginResponse($request);
    }
    public function showLoginForm()
    {
//        return view('pages-maintenance');
        return view('auth.login');
    }
}
