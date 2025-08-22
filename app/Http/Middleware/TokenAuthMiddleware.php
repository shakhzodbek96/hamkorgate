<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TokenAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('token')) {
            try {
                $token = $request->token;
                $user = User::where('otp_config->one_time_token', $token)
                    ->first();

                if (!$user) {
                    return redirect()->route('login')->with('error', 'Неверный токен или срок токена истек.');
                }

                if ($user->otp_config['one_time_token_expires_at'] < now()) {
                    $user->chat_id = null;
                    $user->save();
                    return redirect()->route('login')->with('error', 'Срок токена истек.');
                }

                $otp_data = [
                    'otp_code' => null,
                    'otp_sent_at' => null,
                    'otp_expires_at' => null,
                    'otp_login_attempts' => 0,
                    'otp_login_at' => now(),
                    'otp_login_ip' => $request->ip(),
                    'one_time_token' => null,
                    'one_time_token_expires_at' => null
                ];

                $user->otp_config = $otp_data;
                $user->save();

                Auth::login($user);
                return redirect()->route('home');
            } catch (\Exception $e) {
                return redirect()->route('login')->withErrors('Invalid token format');
            }
        }

        return $next($request);
    }
}
