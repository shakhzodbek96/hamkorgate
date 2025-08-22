<?php

namespace App\Http\Controllers\Api;

use App\Models\Partner;
use App\Services\Helpers\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Login extends Response
{
    public function login(Request $request)
    {
        $v = $this->validate($request->all(),[
            'method' => 'required',
            'params' => 'array|max:250',
            'params.username' => 'required|string|max:255',
            'params.password' => 'required|string|max:255',
            'params.token' => 'string|min:32',
        ]);
        if ($v !== true) return $v;

        // Check partners config:username and password
        $partner = Partner::where('config->auth->username', $request->params['username'])
            ->where('config->auth->password', $request->params['password'])
            ->first();

        // Increment login attempts by IP address and block when attempts over 5, save all in cache for 1 hour
        if (!$partner) {
            $cacheKey = 'login_attempts_'.$request->ip();
            if (Cache::has($cacheKey)) {
                $attempts = Cache::get($cacheKey);
                if ($attempts >= 5) {
                    return self::errorResponse("Too many login attempts! Try again later!");
                }
                Cache::increment($cacheKey);
            } else {
                Cache::put($cacheKey,1,3600);
            }
            return self::errorResponse("Username or password is incorrect!");
        }
        if ($request->has('params.token')) {
            if (Partner::where('config->auth->token', $request->params['username'])->exists()) {
                return self::errorResponse("Given token not acceptable!");
            }
            $config = $partner->config;
            $config['auth']['token'] = $request->get('params')['token'];
        } else {
            $config = $partner->config;
            $config['auth']['token'] = Str::random(40).Str::ulid();
        }

        $partner->config = $config; // Assign back the modified array
        $partner->save();

        // Clear login attempts cache
        Cache::forget('login_attempts_'.$request->ip());
        return self::successResponse([
            'token' => $partner->config['auth']['token']
        ]);
    }
}
