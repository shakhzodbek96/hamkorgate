<?php

namespace App\Services\Hamkor;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;

class HbA2cService
{
    private Client $http;
    private string $baseUrl;
    private string $tokenEndpoint;
    private string $key;
    private string $secret;
    private string $tokenCacheKey = 'hamkor.access_token';

    public function __construct()
    {
        $this->baseUrl       = rtrim(config('hamkor.base_url'), '/');
        $this->tokenEndpoint = config('hamkor.token_endpoint');
        $this->key           = (string) config('hamkor.key');
        $this->secret        = (string) config('hamkor.secret');

        $options = [
            'timeout'         => (float) config('hamkor.timeout'),
            'connect_timeout' => (float) config('hamkor.connect_timeout'),
        ];

        // mTLS (enable when certs are ready)
        $mtls = config('hamkor.mtls');
        if (!empty($mtls['enabled'])) {
            if (!empty($mtls['cert_path'])) $options['cert'] = $mtls['cert_path'];
            if (!empty($mtls['key_path']))  $options['ssl_key'] = $mtls['key_path'];
            if (!empty($mtls['key_pass']))  $options['password'] = $mtls['key_pass'];
            $options['verify'] = $mtls['ca_path'] ?: true;
        }

        $this->http = new Client($options);
    }

    /* -------------------- Public API methods -------------------- */

    public function cardListByPhone(string $phone): array
    {
        $url = "{$this->baseUrl}/inttransfer/v2/v2/phone/{$phone}/cards";
        return $this->request('GET', $url, []);
    }

    public function nmtCheck(
        string $accType,
        string $account,
        int|string $amountMinor,
        string $currencyAlpha3,
        string $payId,
        ?string $settlementCurr = null
    ): array {
        $body = [
            'action'   => 'nmtcheck',
            'acc_type' => $accType,
            'account'  => $account,
            'amount'   => (string) $amountMinor,
            'currency' => $currencyAlpha3,
            'pay_id'   => $payId,
        ];
        if ($settlementCurr !== null) $body['settlement_curr'] = $settlementCurr;

        $url = "{$this->baseUrl}/inttransfer/v2/v2/universal-method";
        return $this->request('POST', $url, $body);
    }

    public function clientCheck(
        string $payId,
        string $idNumber,
        string $senderBirthday, // dd.MM.yyyy
        string $senderSurname,
        string $senderName,
        ?string $idSeries = null,
        ?string $senderMiddleName = null
    ): array {
        $body = [
            'action'          => 'clientcheck',
            'pay_id'          => $payId,
            'id_number'       => $idNumber,
            'sender_birthday' => $senderBirthday,
            'sender_surname'  => $senderSurname,
            'sender_name'     => $senderName,
        ];
        if ($idSeries !== null)         $body['id_series'] = $idSeries;
        if ($senderMiddleName !== null) $body['sender_middle_name'] = $senderMiddleName;

        $url = "{$this->baseUrl}/inttransfer/v2/v2/universal-method";
        return $this->request('POST', $url, $body);
    }

    public function payment(string $payId, string $payDate): array
    {
        $url  = "{$this->baseUrl}/inttransfer/v2/v2/universal-method";
        $body = [
            'action'   => 'payment',
            'pay_id'   => $payId,
            'pay_date' => $payDate, // dd.MM.yyyy_HH:mm:ss
        ];
        return $this->request('POST', $url, $body);
    }

    public function getStatus(string $payId): array
    {
        $url  = "{$this->baseUrl}/inttransfer/v2/v2/universal-method";
        $body = ['action' => 'getstatus', 'pay_id' => $payId];
        return $this->request('POST', $url, $body);
    }

    /* -------------------- Core request helpers -------------------- */

    private function request(string $method, string $url, array $payload): array
    {
        $headers = ['Accept' => 'application/json'];
        $token   = $this->getAccessToken();
        $headers['Authorization'] = 'Bearer ' . $token;

        $make = function () use ($method, $url, $headers, $payload) {
            if ($method === 'GET') {
                return $this->http->request('GET', $url, [
                    'headers' => $headers,
                    'query'   => $payload,
                ]);
            }
            return $this->http->request('POST', $url, [
                'headers' => $headers + ['Content-Type' => 'application/json'],
                'json'    => $payload,
            ]);
        };

        try {
            $res  = $make();
            $json = json_decode((string) $res->getBody(), true);
            return is_array($json) ? $json : ['data' => 'invalid_json_response'];
        } catch (RequestException $e) {
            $status = $e->getResponse()?->getStatusCode();

            // Auto refresh on 401 once
            if ($status === 401) {
                $this->forgetToken();
                $new = $this->refreshToken();

                $headers['Authorization'] = 'Bearer ' . $new;
                try {
                    $res2  = ($method === 'GET')
                        ? $this->http->request('GET', $url, ['headers' => $headers, 'query' => $payload])
                        : $this->http->request('POST', $url, ['headers' => $headers + ['Content-Type' => 'application/json'], 'json' => $payload]);

                    $json2 = json_decode((string) $res2->getBody(), true);
                    return is_array($json2) ? $json2 : ['data' => 'invalid_json_response_after_refresh'];
                } catch (\Throwable $e2) {
                    // fall through
                }
            }

            if ($e->hasResponse()) {
                $body = (string) $e->getResponse()->getBody();
                $dec  = json_decode($body, true);
                return is_array($dec) ? $dec : ['data' => $body ?: 'http_error', 'status' => $status];
            }
            return ['data' => 'network_error: ' . $e->getMessage()];
        } catch (\Throwable $e) {
            return ['data' => 'unexpected_error: ' . $e->getMessage()];
        }
    }

    /* -------------------- Token lifecycle -------------------- */

    public function getAccessToken(): string
    {
        $cached = Cache::get($this->tokenCacheKey);
        if (!empty($cached)) return $cached;
        return $this->refreshToken();
    }

    public function refreshToken(): string
    {
        $resp = $this->http->post($this->baseUrl . $this->tokenEndpoint, [
            'auth'    => [$this->key, $this->secret], // Basic Auth
            'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
            'json'    => ['grant_type' => 'client_credentials'],
        ]);

        $json = json_decode((string) $resp->getBody(), true);
        if (!is_array($json) || empty($json['access_token'])) {
            throw new \RuntimeException('Hamkor token response invalid');
        }

        $token      = $json['access_token'];
        $expiresIn  = (int) ($json['expires_in'] ?? 3600);
        $buffer     = 60;
        $ttl        = max(60, $expiresIn - $buffer);

        Cache::put($this->tokenCacheKey, $token, $ttl);
        return $token;
    }

    public function forgetToken(): void
    {
        Cache::forget($this->tokenCacheKey);
    }
}
