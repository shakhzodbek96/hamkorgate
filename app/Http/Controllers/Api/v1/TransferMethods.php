<?php

namespace App\Http\Controllers\Api\v1;

use App\Services\Helpers\Response;

class TransferMethods extends Response
{
    public function service_info(array $params): array
    {
        if (!isset($params['rate']) || !is_bool($params['rate'])) {
            return self::errorResponse("Invalid rate parameter");
        }

        if (!isset($params['commission']) || !is_bool($params['commission'])) {
            return self::errorResponse("Invalid commission parameter");
        }

        return self::successResponse([
            'rate' => $params['rate'] ? [] : null,
            'commission' => $params['commission'] ? [] : null,
        ]);
    }

    public function receiver_check(array $params): array
    {
        $this->validate($params, [
            'card_number' => 'required|string|min:16|max:16',
        ]);

        // Example static response (stub)
        return self::successResponse([
            'card_number'   => $params['card_number'],
            'status'        => 0, // 0 = success, 1 = error etc.
            'bank'          => 'Hamkorbank',
            'cardholder'    => 'John Doe', // optional extra
        ]);
    }

    public function create(array $params): array
    {
        $this->validate($params, [
            'ext_id'    => 'required|string|max:64',
            'amount'    => 'required|integer|min:1',
            'currency'  => 'required|string|size:3', // e.g. 840, 643
            'receiver'  => 'required|string|min:16|max:32', // card/token
            'sender'    => 'required|array',
        ]);

        return self::successResponse([
            'ext_id' => $params['ext_id'],

            'debit' => [
                'form_url'   => 'https://pay.example.com/form/' . $params['ext_id'],
                'amount'     => $params['amount'],
                'currency'   => $params['currency'],
                'commission' => 1500, // fake
                'state'      => 0,
                'description'=> 'created',
            ],

            'credit' => [
                'amount'     => $params['amount'] - 1500,
                'currency'   => $params['currency'],
                'commission' => 0,
                'description'=> 'receiver credited',
            ],

            'sender' => [
                'name'    => $params['sender']['name'] ?? 'John Doe',
                'card'    => $params['sender']['card'] ?? '8600123412341234',
            ],

            'receiver' => [
                'card'    => $params['receiver'],
                'bank'    => 'Hamkorbank', // fake
            ],
        ]);
    }


    public function state(array $params): array
    {
        $this->validate($params, [
            'ext_id' => 'required|string|max:64',
        ]);

        return self::successResponse([
            'ext_id' => $params['ext_id'],

            'debit' => [
                'amount'      => 100000, // fake
                'currency'    => '840',
                'commission'  => 1500,
                'state'       => 4, // success
                'description' => 'debit processed',
            ],

            'credit' => [
                'amount'      => 98500,
                'currency'    => '840',
                'commission'  => 0,
                'state'       => 4, // success
                'description' => 'credit successful',
            ],

            'sender' => [
                'name' => 'Ali Valiyev',
                'card' => '9860123412341234',
            ],

            'receiver' => [
                'card' => '8600123412341234',
                'bank' => 'Hamkorbank',
            ],
        ]);
    }



}
