<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$BASE_URL = "https://host/inttransfer/v2/v2";
$UNI_URL = $BASE_URL . "/universal-method";


function cardListByPhone($phone): array
{
    global $BASE_URL;
    $url = $BASE_URL . "/phone/{$phone}/cards";
    $payload = [];

    return fire($payload, $url, "GET");
}

/**
 * POST /universal-method (action=nmtcheck)
 */
function nmtCheck($acc_type, $account, $amount, $currency, $pay_id, $settlement_curr = null): array
{
    global $UNI_URL;

    $payload = [
        "action" => "nmtcheck",
        "acc_type" => $acc_type,
        "account" => $account,
        // keep money as string to match partner examples
        "amount" => (string)$amount,
        "currency" => $currency,
        "pay_id" => $pay_id,
    ];

    if (!is_null($settlement_curr)) {
        $payload["settlement_curr"] = $settlement_curr;
    }

    return fire($payload, $UNI_URL, "POST");
}

/**
 * POST /universal-method (action=clientcheck)
 */
function clientCheck($pay_id, $id_number, $sender_birthday, $sender_surname, $sender_name, $id_series = null, $sender_middle_name = null): array
{
    global $UNI_URL;

    $payload = [
        "action" => "clientcheck",
        "pay_id" => $pay_id,
        "id_number" => $id_number,
        "sender_birthday" => $sender_birthday,   // dd.MM.yyyy
        "sender_surname" => $sender_surname,
        "sender_name" => $sender_name,
    ];

    if (!is_null($id_series)) {
        $payload["id_series"] = $id_series;
    }
    if (!is_null($sender_middle_name)) {
        $payload["sender_middle_name"] = $sender_middle_name;
    }

    return fire($payload, $UNI_URL, "POST");
}

/**
 * POST /universal-method (action=payment)
 */
function payment($pay_id, $pay_date): array
{
    global $UNI_URL;

    $payload = [
        "action" => "payment",
        "pay_id" => $pay_id,
        // dd.MM.yyyy_HH:mm:ss (e.g., 16.08.2024_08:45:50)
        "pay_date" => $pay_date,
    ];

    return fire($payload, $UNI_URL, "POST");
}

/**
 * POST /universal-method (action=getstatus)
 */
function getStatus($pay_id): array
{
    global $UNI_URL;

    $payload = [
        "action" => "getstatus",
        "pay_id" => $pay_id,
    ];

    return fire($payload, $UNI_URL, "POST");
}

/**
 * Low-level HTTP caller
 */
function fire($payload, string $url, string $method = "POST"): array
{
    static $client = null;
    if ($client === null) {
        // No base_uri since we pass absolute URLs
        $client = new Client([
            'timeout' => 15,
            'connect_timeout' => 5,
        ]);
    }

    $headers = ['Accept' => 'application/json'];

    $token = getenv('PARTNER_BEARER_TOKEN');
    if (!empty($token)) {
        $headers['Authorization'] = 'Bearer ' . $token;
    }

    try {
        if ($method === "GET") {
            $res = $client->request('GET', $url, [
                'headers' => $headers,
                'query' => $payload,
            ]);
        } else {
            $res = $client->request('POST', $url, [
                'headers' => $headers + ['Content-Type' => 'application/json'],
                'json' => $payload,
            ]);
        }

        $body = (string)$res->getBody();
        $json = json_decode($body, true);

        if (is_array($json)) {
            return $json;
        }
        return ["data" => "invalid_json_response"];
    } catch (RequestException $e) {
        // Try to surface partner's error payload if present
        if ($e->hasResponse()) {
            $errBody = (string)$e->getResponse()->getBody();
            $decoded = json_decode($errBody, true);
            if (is_array($decoded)) {
                return $decoded;
            }
            return ["data" => $errBody !== "" ? $errBody : "http_error"];
        }
        return ["data" => "network_error: " . $e->getMessage()];
    } catch (\Throwable $e) {
        return ["data" => "unexpected_error: " . $e->getMessage()];
    }
}
