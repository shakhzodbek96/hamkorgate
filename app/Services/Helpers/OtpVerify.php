<?php

namespace App\Services\Helpers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class OtpVerify
{
    static function forbiddenAccess():void
    {
        header('HTTP/1.1 403 Forbidden');
        die();
    }

    static function verify($otp, $userId)
    {
        $userOtp = $otp;
        $storedOtp = Cache::get('otp_code_' . $userId);

        if ($userOtp == $storedOtp) {
            Cache::forget('otp_code_' . $userId);
            return true;
        }

        return false;
    }

}
