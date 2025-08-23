<?php

namespace App\Http\Controllers\Faker;

use App\Http\Controllers\Controller;
use App\Services\Hamkor\HbA2cServiceFaker;


class A2CFakerPartnerController extends Controller
{
    public function __construct(private HbA2cServiceFaker $service) {}

    public function cardListByPhone(string $phone)
    {
        $res = $this->service->cardListByPhone($phone);
        return response()->json($res, 200, [], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }

    public function nmtCheck(Request $request)
    {
        // Minimal validation (adjust as needed)
        $request->validate([
            'action'   => 'required|string',
            'acc_type' => 'required|string',
            'account'  => 'required|string',
            'amount'   => 'required',
            'currency' => 'required|string',
            'pay_id'   => 'required|string',
            // settlement_curr optional
        ]);

        $res = $this->service->nmtCheck($request->all());
        return response()->json($res, 200, [], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }

    public function clientCheck(Request $request)
    {
        $request->validate([
            'action'            => 'required|string',
            'pay_id'            => 'required|string',
            'id_series'         => 'nullable|string',
            'id_number'         => 'nullable|string',
            'sender_birthday'   => 'nullable|string',
            'sender_surname'    => 'nullable|string',
            'sender_name'       => 'nullable|string',
            'sender_middle_name'=> 'nullable|string',
        ]);

        $res = $this->service->clientCheck($request->all());
        return response()->json($res, 200, [], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }

    public function payment(Request $request)
    {
        $request->validate([
            'action'  => 'required|string',
            'pay_id'  => 'required|string',
            'pay_date'=> 'nullable|string', // "dd.mm.YYYY_HH:mm:ss"
        ]);

        $res = $this->service->payment($request->all());
        return response()->json($res, 200, [], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }

    public function getStatus(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'pay_id' => 'required|string',
        ]);

        $res = $this->service->getStatus($request->all());
        return response()->json($res, 200, [], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
}
