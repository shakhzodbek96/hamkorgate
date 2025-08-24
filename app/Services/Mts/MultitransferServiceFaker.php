<?php

namespace App\Services\Mts;

use Illuminate\Support\Facades\Cache;

class MultitransferServiceFaker
{
    private const TTL = 3600; // 1h

    // States
    public const CREATED     = 0;
    public const FORM_OPENED = 1;
    public const REQ_3DS     = 2;
    public const PROCESSING  = 3;
    public const SUCCESS     = 4;
    public const FAILED      = 5;

    private function key(string $extId): string
    {
        return "mts:debit_faker:$extId";
    }

    private function nowFmt(): string
    {
        return now()->format('d.m.Y_H:i:s'); // dd.mm.YYYY_HH:ii:ss
    }

    private function newExtId(): string
    {
        return (string) random_int(10_000_000_000_000_000, 99_999_999_999_999_999);
    }

    public function create(array $req): array
    {
        $amount = $req['amount'] ?? null;
        $desc   = $req['description'] ?? null;
        $curr   = $req['currency'] ?? 'UZS';

        if ($amount === null || $desc === null) {
            return ['data' => ['code'=>'2','message'=>'Invalid parameters']];
        }

        $extId = $this->newExtId();

        $payload = [
            'ext_id'      => $extId,
            'amount'      => (string)$amount,
            'currency'    => $curr,
            'description' => (string)$desc,
            'state'       => self::CREATED,
            'card'        => null,
            'created_at'  => $this->nowFmt(),
        ];

        Cache::put($this->key($extId), $payload, self::TTL);

        return ['data' => [
            'code'    => '0',
            'message' => 'Успешное завершение операции',
            'ext_id'  => $extId,
            'form_url'=> route('pay.form', ['ext_id' => $extId]),
            'debit'   => [
                'amount'      => (string)$amount,
                'currency'    => $curr,
                'state'       => self::CREATED,
                'description' => (string)$desc,
            ],
        ]];
    }

    public function get(string $extId): ?array
    {
        return Cache::get($this->key($extId));
    }

    public function set(string $extId, array $data): void
    {
        Cache::put($this->key($extId), $data, self::TTL);
    }

    public function statePayload(array $p): array
    {
        return ['data' => [
            'code'        => '0',
            'message'     => 'Успешное завершение операции',
            'ext_id'      => $p['ext_id'],
            'state'       => $p['state'],
            'debit'       => [
                'amount'      => $p['amount'],
                'currency'    => $p['currency'],
                'description' => $p['description'],
            ],
            'card_masked' => $p['card']['masked'] ?? null,
            'updated_at'  => $this->nowFmt(),
        ]];
    }
}
