<?php

namespace App\Http\Controllers\Faker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Mts\MultitransferServiceFaker as Svc;

class DebitFakerPartnerController extends Controller
{
    public function __construct(private Svc $svc) {}

    // POST /api/a2c/debit/create
    public function create(Request $request)
    {
        $request->validate([
            'amount'      => 'required',
            'description' => 'required|string',
            'currency'    => 'nullable|string',
        ]);

        $res = $this->svc->create($request->all());
        return response()->json($res, 200, [], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }

    // POST /api/a2c/debit/state
    public function state(Request $request)
    {
        $request->validate(['ext_id' => 'required|string']);

        $p = $this->svc->get($request->string('ext_id'));
        if (!$p) {
            return response()->json(['data' => [
                'code' => '2',
                'message' => 'Неверное значение идентификатора транзакции',
            ]], 200, [], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        }

        return response()->json($this->svc->statePayload($p),
            200, [], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
}
