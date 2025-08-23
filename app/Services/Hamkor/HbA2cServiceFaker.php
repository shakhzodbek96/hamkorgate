<?php

namespace App\Services\Hamkor;

class HbA2cServiceFaker
{
    private function ok(array $payload): array
    {
        return ['data' => $payload];
    }

    private function fail(string $reason): array
    {
        return ['data' => $reason];
    }

    private function isErrorPayId(?string $payId): bool
    {
        if (!$payId) return false;
        return (bool)preg_match('/(ERR|INVALID|BAD)/i', $payId);
    }

    private function randExtId(): string
    {
        return strval(random_int(10_000_000_000_000_000, 99_999_999_999_999_999));
    }

    private function nowProtoFmt(): string
    {
        // dd.mm.YYYY_HH:ii:ss (PHP: d.m.Y_H:i:s)
        return date('d.m.Y_H:i:s');
    }

    /** 1) cardListByPhone */
    public function cardListByPhone(string $phone): array
    {
        if (!preg_match('/^\d{9,15}$/', $phone)) {
            return $this->fail("Invalid phone format");
        }

        if (preg_match('/000$/', $phone)) {
            return $this->fail("error description");
        }

        $processing = (intval(substr($phone, -1)) % 2 === 0) ? "HUMO" : "UZCARD";

        return $this->ok([
            [
                "id" => "88520a4ae4c25cc48578bdb2ba3b9201d2e5b990566c4de9fd6ee093b0a79c51",
                "bank_code" => "09012",
                "cardholder_name" => "IVAN IVANOV",
                "masked_pan" => "986000****123456",
                "processing" => $processing
            ],
            [
                "id" => "9e2f0b9d0a2c4f2aa2f01c8e7c9e31b8aa7f5a6d1c3347d889ab1122c3d4e5f6",
                "bank_code" => "20010",
                "cardholder_name" => "IVAN IVANOV",
                "masked_pan" => "860012****789012",
                "processing" => ($processing === "HUMO" ? "UZCARD" : "HUMO")
            ],
        ]);
    }

    /** 2) nmtCheck */
    public function nmtCheck(array $req): array
    {
        $required = ["action", "acc_type", "account", "amount", "currency", "pay_id"];
        foreach ($required as $k) {
            if (!isset($req[$k]) || $req[$k] === "") {
                return $this->ok([
                    "code" => "2",
                    "message" => "Неизвестный тип запроса, не соответствует формату протокола",
                    "credit_amount" => "000",
                    "credit_curr" => "",
                    "curr_rate" => "000",
                    "card" => [
                        "receiver_card" => "0",
                        "receiver_card_id" => "0"
                    ],
                ]);
            }
        }

        if (strtolower($req["action"]) !== "nmtcheck" || $this->isErrorPayId($req["pay_id"])) {
            return $this->ok([
                "code" => "2",
                "message" => "Неизвестный тип запроса, не соответствует формату протокола",
                "credit_amount" => "000",
                "credit_curr" => "",
                "curr_rate" => "000",
                "card" => [
                    "receiver_card" => "0",
                    "receiver_card_id" => "0"
                ],
            ]);
        }

        $amountMinor = (float)$req["amount"];
        $rate = 12.00;
        $creditAmt = number_format($amountMinor / $rate, 2, '.', '');
        $settleCurr = $req["settlement_curr"] ?? "UZS";

        return $this->ok([
            "code" => "0",
            "message" => "Успешное завершение операции",
            "credit_amount" => $creditAmt,
            "credit_curr" => $settleCurr === "RUB" ? "RUB" : "UZS",
            "curr_rate" => number_format($rate, 2, '.', ''),
            "card" => [
                "receiver_card_id" => $req["account"],
                "receiver_card" => "986000****123456",
                "cardholder_name" => "IVAN IVANOV"
            ]
        ]);
    }

    /** 3) clientCheck */
    public function clientCheck(array $req): array
    {
        if (strtolower($req["action"] ?? "") !== "clientcheck" || $this->isErrorPayId($req["pay_id"] ?? null)) {
            return $this->ok([
                "code" => "2",
                "message" => "Неизвестный тип запроса, не соответствует формату протокола"
            ]);
        }

        return $this->ok([
            "code" => "0",
            "message" => "Успешное завершение операции"
        ]);
    }

    /** 4) payment */
    public function payment(array $req): array
    {
        if (strtolower($req["action"] ?? "") !== "payment" || $this->isErrorPayId($req["pay_id"] ?? null)) {
            return $this->ok([
                "code" => "2",
                "message" => "Неизвестный тип запроса, не соответствует формату протокола",
                "ext_id" => $this->randExtId(),
                "reg_date" => $req["pay_date"] ?? $this->nowProtoFmt()
            ]);
        }

        return $this->ok([
            "code" => "0",
            "message" => "Успешное завершение операции",
            "ext_id" => $this->randExtId(),
            "reg_date" => $req["pay_date"] ?? $this->nowProtoFmt()
        ]);
    }

    /** 5) getStatus */
    public function getStatus(array $req): array
    {
        $action = strtolower($req["action"] ?? "");
        if ($action !== "getstatus" || $this->isErrorPayId($req["pay_id"] ?? null)) {
            return $this->ok([
                "code" => "2",
                "message" => "Неверное значение идентификатора транзакции",
                "ext_id" => "",
                "reg_date" => ""
            ]);
        }

        return $this->ok([
            "code" => "0",
            "message" => "Успешное завершение операции",
            "ext_id" => $this->randExtId(),
            "reg_date" => $this->nowProtoFmt()
        ]);
    }
}
