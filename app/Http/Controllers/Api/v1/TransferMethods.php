<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Faker\A2CFakerPartnerController;
use App\Models\Transfer;
use App\Services\Hamkor\HbA2cServiceFaker;
use App\Services\Hamkor\HbA2cService;
use App\Services\Helpers\Response;
use Illuminate\Support\Str;


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
        $hamkor =new HbA2cService();

// Token (auto-cached)
        $token = $hamkor->getAccessToken();
        dd($token);

        return self::successResponse([
            'rate' => $params['rate'] ? [] : null,
            'commission' => $params['commission'] ? [] : null,
        ]);
    }

    public function receiver_check(array $params): array
    {
        $this->validate($params, [
            'account' => 'required|string|min:12|max:32',
        ]);

        $account = preg_replace('/\D+/', '', $params['account']); // strip non-digits
        $faker = app(HbA2cServiceFaker::class);
        // Case 1: if account looks like Uzbek phone number (starts 998 + 9 digits)
        if (preg_match('/^998\d{9}$/', $account)) {
            // Call A2CFakerPartnerController::cardListByPhone
            $res = $faker->cardListByPhone($account);
            return $res; // already returns ["data" => ...]
        }

        // Case 2: if account looks like card number (16–19 digits) or token (>=32 chars)
        if (strlen($account) >= 16) {


            $payId = sprintf(
                'check_req_%s_%s',
                now()->format('Ymd'),
                (string)Str::uuid()
            );

            $res = $faker->nmtCheck([
                'action' => 'nmtcheck',
                'acc_type' => (strlen($account) >= 32) ? 'CARD_TOKEN' : 'CARD',
                'account' => $account,
                'amount' => '10000',     // fake test amount
                'currency' => 'UZS',       // default faker currency
                'pay_id' => $payId,
                'settlement_curr' => 'UZS',
            ]);
            return $res;
        }

        // Default stub fallback
        return self::successResponse([
            'account' => $params['account'],
            'status' => 1,
            'bank' => null,
            'cardholder' => null,
            'message' => 'Unsupported account format',
        ]);
    }


    public function create(array $params): array
    {
        $this->validate($params, [
            'ext_id' => 'required|string|max:64',
            'amount' => 'required|integer|min:1',
            'currency' => 'required|string|size:3', // e.g. 860, 643
            'receiver' => 'required|string|min:16|max:32', // card/token
            'sender' => 'required|array',
        ]);


        // check ext_id is unique for partner
        $partnerId = $params['partner_id'];
        if (Transfer::where('partner_id', $partnerId)->where('ext_id', $params['ext_id'])->exists()) {
            return self::errorResponse("ext_id already exists for this partner");
        }

        // check currency
        $allowed = ['860', '643'];
        if (!in_array($params['currency'], $allowed)) {
            return self::errorResponse("Invalid currency code");
        }

        // check receiver type
        $receiverType = (strlen($params['receiver']) >= 32) ? 'TOKEN' : 'CARD';

        // check sender required fields
        foreach (['name', 'surname', 'series', 'birth_date'] as $f) {
            if (empty($params['sender'][$f])) {
                return self::errorResponse("sender.$f is required");
            }
        }

        // commission (TODO: replace with rate API later)
        $commission = 1500;
        $amount = (int)$params['amount'];

        // save transfer
        $transfer = Transfer::create([
            'partner_id' => $partnerId,
            'ext_id' => $params['ext_id'],
            'receiver_account_type' => $receiverType,
            'receiver_account' => $params['receiver'],
            'debit_amount' => $amount,
            'debit_currency' => $params['currency'],
            'debit_description' => 'created',
            'debit_commission' => $commission,
            'debit_form_url' => route('pay.form', ['ext_id' => $params['ext_id']]),
            'credit_stage' => 'nmtCheck',
            'credit_amount' => $amount - $commission,
            'credit_currency' => $params['currency'],
            'sender_account' => $params['sender']['card'] ?? null,
            'sender_name' => $params['sender']['name'],
            'sender_surname' => $params['sender']['surname'],
            'sender_series' => $params['sender']['series'],
            'sender_birth_date' => $params['sender']['birth_date'],
        ]);

        return self::successResponse([
            'ext_id' => $transfer->ext_id,

            'debit' => [
                'form_url' => $transfer->debit_form_url,
                'amount' => $transfer->debit_amount,
                'currency' => $transfer->debit_currency,
                'commission' => $transfer->debit_commission,
                'state' => 0,
                'description' => $transfer->debit_description,
            ],

            'credit' => [
                'amount' => $transfer->credit_amount,
                'currency' => $transfer->credit_currency,
                'commission' => 0,
                'description' => 'receiver credited',
            ],

            'sender' => [
                'name' => $transfer->sender_name,
                'card' => $transfer->sender_account,
            ],

            'receiver' => [
                'card' => $transfer->receiver_account,
                'bank' => 'Hamkorbank', // fake
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
                'amount' => 100000, // fake
                'currency' => '840',
                'commission' => 1500,
                'state' => 4, // success
                'description' => 'debit processed',
            ],

            'credit' => [
                'amount' => 98500,
                'currency' => '840',
                'commission' => 0,
                'state' => 4, // success
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
